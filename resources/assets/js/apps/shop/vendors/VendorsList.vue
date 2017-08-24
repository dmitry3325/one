<template>
    <div>

        <div class="row">
            <div class="col-md-1">
                <button class="btn btn-success">Создать</button>
            </div>
            <div class="col-md-11">
                <input class="form-control" @keyup="search($event.target.value)" placeholder="Поиск"/>
            </div>
        </div>
        <div class="list ">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <table v-if="Object.keys(items).length > 0" class="table table-bordered">
                    <thead>
                    <tr>
                        <th v-for="field in fields">{{field.title}}</th>
                        <th>Картинка</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td v-for="(field, key) in fields"><input @change="updateField($event, item['id'], key)"
                                                                  v-bind:value="item[key]" :disabled="field.disabled">
                        </td>
                        <td>
                            <img v-if="item['goods_photo'].length > 0" v-for="img in item['goods_photo']"
                                 :src="img.path" width="100px"/>
                            <input v-if="item['goods_photo'].length === 0" type="file"
                                   @change="onFileChange($event, item['id'])">
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
                'items': {},
                'fields': {},
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
                let options = {};
                options[field] = event.target.value;

                Data.vendors.update(id, options);
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
            onFileChange(e, id) {
                let files = e.target.files || e.dataTransfer.files;
                console.log(files);

                if (!files.length) {
                    return;
                }
                this.createImage(files[0]);
            },
            createImage(file) {
                let image = new Image();
                let reader = new FileReader();

                reader.onload = (e) => {
                    options['goods_photo']['path'] = e.target.result;
                    Data.vendors.update(id, options);

                };
                reader.readAsDataURL(file);
            },
        }
    });
</script>