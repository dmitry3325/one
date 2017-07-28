<template>
    <div>
        <div class="tool-bar">
            <ol class="breadcrumb d-flex">
                <li v-if="selected" class="breadcrumb-item"><a target="_blank"
                                                               :href="selected.url">{{selected.title}}</a></li>
                <div class="ml-3 ml-auto">
                    <button type="button" class="btn btn-info" @click="showFiltersSelector">Добавить фильтр</button>
                </div>
                <div class="ml-3">
                    <button type="button" class="btn btn-warning" @click="showFieldsEditor">Настроить поля</button>
                </div>
                <div class="ml-3">
                    <button type="button" class="btn btn-success" @click="createEntity($event)">Создать</button>
                </div>
            </ol>
        </div>
        <div class="list">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <table v-if="Object.keys(items).length > 0" class="table table-bordered">
                    <thead>
                    <tr>
                        <th v-for="field in fields">{{(fields_info[field]) ? fields_info[field].title : field}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td v-for="field in fields">{{prepareField(item[field], field)}}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-default wh-100 cursor-pointer"><span
                                    class="glyphicon glyphicon-edit"></span></button>
                        </td>
                    </tr>
                    </tbody>

                </table>
                <div v-else class="text-center"><h3>Ничего не нашлось</h3></div>
            </div>
        </div>
    </div>
</template>
<script>
    let FilterSelector = require('../../../../components/filtersSelector.vue');
    module.exports = Vue.extend({
        data: function () {
            return {
                'entity': null,
                'loading': true,
                'fields_info': {},
                'items': {},
                'selected': {},
                'filters': {},
                'fields': {},
            }
        },
        mounted: function () {
            this.loadData();
            this.showFieldsEditor();
        },
        methods: {
            loadData() {
                let self = this;

                if (!this.filters || !Object.keys(this.filters).length) {
                    this.filters = Ls.get(this._getFiltersKey());
                }

                if (this.filters) Url.set('filters', this.filters);
                else Url.unset('filters');

                let q = [
                    Data.entity.getAllFields(this.entity),
                    Data.entity.getItemsList(this.entity, {
                        'filters': this.filters,
                        'fields': this.fields
                    })
                ];


                if (!Object.keys(self.fields).length) {
                    q.push(Data.entity.getBaseFields(this.entity));
                }

                Ajax.when(q, function (fields, items, fields_list) {
                    self.loading = false;
                    self.fields_info = fields;
                    if (items.result && items.list) {
                        self.items = items.list;
                        self.$forceUpdate();
                    }

                    if (fields_list) {
                        self.fields = fields_list;
                    }
                });

            },
            createEntity($event) {
                let entity = this.entity;
                $event.target.classList.add('disabled');
                Ajax.post('/shop/lists', 'createEntity', {
                    entity: entity
                }, function (res) {
                    if (res.result && res.id) {
                        window.open('/shop/item?type=' + entity + '&id=' + res.id, '_blank');
                    }
                    $event.target.classList.remove('disabled');
                });
            },
            prepareField(value, key) {
                return value;
            },
            showFieldsEditor(e) {

            },
            showFiltersSelector() {
                let self = this;
                new FilterSelector({
                    el: this.setTarget('#goodsAdmin'),
                    data: {
                        'entity': this.entity,
                        'ls_storage_key': this._getFiltersKey(),
                        'filters': ((this.filters) ? this.filters : []),
                        'callback': function (filters) {
                            Url.set('filters', filters);
                            self.loadData();
                        }
                    }
                });
            },
            _getFiltersKey() {
                return 'filters_selector:tab=' + this.entity;
            }
        }
    });
</script>
<style>
    .row-item {
        cursor: pointer;
    }
</style>