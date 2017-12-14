<template>
    <div>

        <div class="row mb-2">
            <div class="col-md-1">
                <button class="btn btn-success" @click="createPage()">Создать</button>
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
                        <th></th>
                        <th v-for="(field, key) in fields"
                            @click="sortChangeRevers(); orderByField = key; items = this.Funs.sortSubObj(items, orderByField, sortRevers);">
                            {{field.title}}
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td>
                            <button @click="edit(item)" type="button" class="btn btn-sm btn-default wh-100 cursor-pointer">
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                        </td>
                        <td v-for="(field, key) in fields">
                            <input v-model="item[key]" @change="updateField($event, item['id'], key)"
                                   :disabled="field.disabled"/>
                        </td>
                        <td>
                            <button class="btn btn-danger" @click="deletePage(item['id'])">Удалить</button>
                        </td>
                    </tr>
                    </tbody>

                </table>
                <div v-else class="text-center"><h3>Ничего не нашлось</h3></div>
            </div>
        </div>

        <!--окошко редактирования-->
        <div class="edit-window" v-if="showEdit">
            <span class="glyphicon glyphicon-remove close" @click="showEdit = false"></span>
            <edit-page :item="editedItem"></edit-page>
        </div>
    </div>
</template>
<script>
    let ConfirmModal = require('../../../components/confirmModal.vue');
    let editPage = require('./pageEdit.vue');

    module.exports = Vue.extend({
        components: {
            'edit-page': editPage,
        },
        data: function () {
            return {
                loading: true,
                items: [],
                fields: {},
                orderByField: 'id',
                sortRevers: 'DESC',

                showEdit: false,
                editedItem: null,
            }
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            loadData() {
                let self = this;

                let q = [
                    Data.htmlPages.getAllFields(),

                    Data.htmlPages.getHtmlPagesList(),
                ];

                Ajax.when(q, function (fields, items) {
                    self.loading = false;
                    self.fields = fields.data;

                    if (items.result && items.data) {
                        self.items = items.data;
                    }

                });

            },
            updateField(event, id, field) {
                let self = this;

                let options = {};
                options[field] = event.target.value;

                Data.htmlPages.update(id, options);

                let sort = Funs.sortSubObj(self.items, self.orderByField, self.sortRevers);

                self.$set(self, 'items', sort);
            },
            deletePage(id) {
                let self = this;

                new ConfirmModal({
                    'data': {
                        'body': 'Уверены, что хотите удалить страницу безвозвратно?',
                        'confirm_func': function () {
                            Data.htmlPages.delete(id).then(function (data) {
                                if (data.result) {
                                    let result = [];

                                    for (let i = 0; i < self.items.length; i++) {
                                        if (self.items[i]['id'] != id) {
                                            result.push(self.items[i]);
                                        }
                                    }
                                    self.items = result;

                                    AppNotifications.add({
                                        'type': 'success',
                                        'body': 'Страница удалена'
                                    })
                                }
                            });
                        }
                    }
                });

            },
            createPage() {
                let self = this;

                new ConfirmModal({
                    'data': {
                        'body': 'Создать новую страницу?',
                        'confirm_func': function () {
                            Data.htmlPages.create().then(function (data) {
                                if (data.result) {
                                    self.items.unshift(data.data);

                                    AppNotifications.add({
                                        'type': 'success',
                                        'body': 'Страниа создана'
                                    });
                                }
                            });
                        }
                    }
                });
            },
            search(value) {
                let self = this;
                Data.htmlPages.getHtmlPagesList().then(function (all) {

                    let result = [];

                    for (let i = 0; i < all.length; i++) {
                        if (all[i]['id'] == value || all[i]['title'].indexOf(value) != -1) {
                            result.push(all[i]);
                        }
                    }

                    self.$set(self, 'items', result);
                });
            },
            sortChangeRevers() {
                let self = this;

                let result = (self.sortRevers == 'DESC') ? 'ASC' : 'DESC';

                self.$set(self, 'sortRevers', result);
            },
            edit(item) {
                let self = this;

                self.showEdit = true;
                self.editedItem = item;
                console.log(item);
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

    .edit-window {
        position: fixed;
        background: #fff;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2%;
    }

    .close {
        position: absolute;
        top: 2%;
        right: 2%;
        cursor: pointer;

    }
</style>