<template>
    <div>

        <div class="row mb-2">
            <div class="col-md-1">
                <button class="btn btn-success" @click="createVendor()">Создать</button>
            </div>
            <div class="col-md-11">
                <input class="form-control" @keyup="search($event.target.value)" placeholder="Поиск"/>
            </div>
        </div>

        <div class="list mt-4">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <table v-if="Object.keys(items).length > 0" class="table table-bordered table-striped">
                    <thead class="thead-inverse">
                    <tr>
                        <th v-for="(field, key) in fields" @click="sortChangeRevers(); orderByField = key; items = this.Funs.sortSubObj(items, orderByField, sortRevers);">{{field.title}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td v-for="(field, key) in fields">
                            <input v-model="item[key]" @change="updateField($event, item['id'], key)" :disabled="field.disabled"  />
                        </td>
                        <td>
                            <button class="btn btn-danger" @click="deleteVendor(item['id'])">Удалить</button>
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
    let ConfirmModal = require('../../../components/confirmModal.vue');

    module.exports = Vue.extend({
        data: function () {
            return {
                'loading': true,
                'items': [],
                'fields': {},
                'orderByField': 'id',
                'sortRevers': 'DESC'
            }
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            loadData() {
                let self = this;

                let q = [
                    Data.vendors.getAllFields(),

                    Data.vendors.getVendorsList({
                        filters: this.filters,
                        fields: this.fields
                    }),
                ];


                Ajax.when(q, function (fields, items) {
                    self.loading = false;
                    self.fields = fields;

                    if (items.result && items.list) {
                        self.$set(self, 'items', items.list);
                    }

                });

            },
            updateField(event, id, field) {
                let self = this;

                let options = {};
                options[field] = event.target.value;

                Data.vendors.update(id, options);

                let sort = Funs.sortSubObj(self.items, self.orderByField, self.sortRevers);

                self.$set(self, 'items', sort);
            },
            deleteVendor(id) {
                let self = this;

                new ConfirmModal({
                    'data': {
                        'body': 'Уверены, что хотите удалить производителя безвозвратно?',
                        'confirm_func': function () {
                            Data.vendors.delete(id).then(function (data) {
                                if (data.result) {
                                    let result = [];

                                    for (let i = 0; i < self.items.length; i++) {
                                        if (self.items[i]['id'] != id) {
                                            result.push(self.items[i]);
                                        }
                                    }

                                    self.$set(self, 'items', result);

                                    AppNotifications.add({
                                        'type': 'success',
                                        'body': 'Производитель удален'
                                    })
                                }
                            });
                        }
                    }
                });

            },
            createVendor(){
                let self = this;

                new ConfirmModal({
                    'data': {
                        'body': 'Создать нового производителя?',
                        'confirm_func': function () {
                            Data.vendors.create().then(function (data) {
                                if (data.result) {
                                    self.items.unshift(data.vendor);

                                    AppNotifications.add({
                                        'type': 'success',
                                        'body': 'Производитель создан'
                                    });
                                }
                            });
                        }
                    }
                });
            },
            search(value) {
                let self = this;
                Data.vendors.getVendorsList().then(function (all) {

                    let result = [];

                    for (let i = 0; i < all.length; i++) {
                        if (all[i]['id'] == value || all[i]['title'].indexOf(value) != -1) {
                            result.push(all[i]);
                        }
                    }

                    self.$set(self, 'items', result);
                });
            },
            sortChangeRevers(){
                let self = this;

                let result = (self.sortRevers == 'DESC') ? 'ASC' : 'DESC';

                self.$set(self, 'sortRevers', result);
            }
        }
    });
</script>


<style lang="scss">
    input {
        background: transparent;
        border: none;
        outline: none;
        width: 100%;
    }
</style>