<template>
    <div id="filtersCont" class="filter-selector">
        <div class="list-block">
            <div v-for="(list,index_or) in filters">
                <div class="alert alert-info">
                    <div v-for="(filter,index_and) in list">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{(filter.field && allFields[filter.field]) ? allFields[filter.field].title : 'Поле'}}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a v-for="(field,field_name) in allFields"
                                           v-if="!filter.typeahead || field.title.indexOf(filter.typeahead)!=-1"
                                           @click="$set(filter,'field',field_name)" class="dropdown-item">
                                            {{field.title}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{(typeof filter.method !== undefined && methods[filter.method]) ? methods[filter.method] : 'Операция'}}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a v-for="(method,method_index) in methods"
                                           @click="$set(filter,'method',method_index)"
                                           class="dropdown-item">
                                            {{method}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input class="input-sm form-control"
                                       v-model="filter.value"
                                       @keyup="$set(filter,'value',$event.target.value)"
                                       placeholder="Значение"/>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger"
                                        @click="$delete(list,index_and);
                                        ((Object.keys(filters).length>1 && Object.keys(list).length==0)?$delete(filters,index_or):'')">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </div>
                        </div>
                        <div class="row" v-if="index_and != (Object.keys(list).length - 1)">
                            <span class="badge badge-default">И</span></div>
                    </div>
                    <div><span @click="$set(list,Object.keys(list).length,{})"
                               class="badge badge-default">Добавить</span></div>
                </div>
                <div class="row" v-if="index_or != (Object.keys(filters).length - 1)">
                    <div class="col-12"><span class="badge badge-default">ИЛИ</span></div>
                </div>
            </div>
            <div>
                        <span @click="$set(filters,Object.keys(filters).length,[{}])"
                              class="badge badge-default">Добавить</span>
            </div>
        </div>
        <div>
            <hr/>
        </div>
        <div>
            <button type="button" @click="save" class="btn btn-success float-right">Сохранить</button>
        </div>
    </div>

</template>
<script>
    module.exports = Vue.extend({
        props:   {
            'entity':         {
                'default': null
            },
            'ls_storage_key': {
                'default': 'selected_filters'
            },
            'filters':        {
                'default': function () {
                    return [{}];
                },
            },
            'callback':       {
                'default': function () {
                    return null;
                }
            }
        },
        data:    function () {
            return {
                'allFields': {},
                'methods':   [],
            };
        },
        mounted() {
            if (!this.entity) {
                return false;
            }

            let self = this;
            Data.entity.getAllFields(this.entity).then(function (flds) {
                self.$set(self, 'allFields', flds);
            });

            Data.entity.getFilterMethods().then(function (methods) {
                self.methods = methods;
            });

            if (!this.filters || !Object.keys(this.filters).length) {
                let filters = null;
                if (this.ls_storage_key) {
                    filters = Ls.get(this.ls_storage_key);
                }

                if (!filters) this.$set(this.filters, 0, [{}]);
                else this.$set(self, 'filters', filters);
            }
        },
        methods: {
            save() {
                if (this.ls_storage_key) {
                    Ls.set(this.ls_storage_key, this.filters);
                }
                if (typeof this.callback === 'function') {
                    this.callback(this.filters);
                }
            }
        }
    });
</script>
<style>
    #filtersCont .badge {
        cursor: pointer;
    }

    #filtersCont .row {
        margin-bottom: 10px;
    }

    #filtersCont .row .btn-danger {
        margin-top: 5px;
        cursor: pointer;
    }
</style>