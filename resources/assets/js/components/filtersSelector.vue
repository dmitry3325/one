<template>
    <div id="filtersCont" class="modal fade" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Выберите фильтры</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-for="(list,index_or) in filters" class="alert alert-info">
                        <div class="row" v-for="(filter,index_and) in list">
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
                                        {{(filter.method) ? filter.method : 'Операция'}}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a v-for="method in methods"
                                           @click="$set(filter,'method',method)"
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
                                <button type="button" class="btn btn-sm btn-danger" @click="$delete(list,index_and);">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <span @click="list.push({})" class="badge badge-default">
                                {{(list.length) ? 'И' : 'Добавить'}}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span @click="filters.push([{}])" class="badge badge-default">
                            {{(filters.length) ? 'Или' : 'Добавить'}}
                        </span>
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
                'ls_storage_key' : 'selected_filters',
                'allFields': {},
                'filters': [],
                'methods': ['=', '>', '<', '>=', '<=', 'LIKE'],
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

            if(!this.filters.length){
                let filters = Ls.get(this.ls_storage_key);
                if(!filters) this.$set(this.filters,0,[{}]);
                else this.$set(self,'filters',filters);
            }

            $(this.$el).modal('show');
            $(this.$el).on('hidden.bs.modal', function (e) {
                self.$el.remove();
                self.$destroy();
            })
        },
        methods:{
            save(e){
                Ls.set(this.ls_storage_key,this.filters);
                $(this.$el).modal('hide');
            }
        }
    });
</script>


<style>
    .badge {
        cursor: pointer;
    }

    .row {
        margin-bottom: 10px;
    }
    .row .btn-danger{
        margin-top: 5px;
        cursor: pointer;
    }
</style>