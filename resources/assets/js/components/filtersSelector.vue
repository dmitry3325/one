<template>
    <div id="filtersCont" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Выберите фильтры</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
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
                <div class="modal-footer">
                    <button type="button" @click="save" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                'entity': null,
                'ls_storage_key': 'selected_filters',
                'allFields': {},
                'filters': [],
                'methods': [],
                'callback': null,
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
                let filters = Ls.get(this.ls_storage_key);
                if (!filters) this.$set(this.filters, 0, [{}]);
                else this.$set(self, 'filters', filters);
            }

            $(this.$el).modal('show');
            $(this.$el).on('hidden.bs.modal', function (e) {
                self.$el.remove();
                self.$destroy();
            });
        },
        methods: {
            save() {
                Ls.set(this.ls_storage_key, this.filters);

                $(this.$el).modal('hide');
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