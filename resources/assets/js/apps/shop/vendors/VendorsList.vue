<template>
    <div>

        <input class="form-control" @keyup="search($event.target.value)" placeholder="Поиск" />
        <br/>
        <div class="list">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <table v-if="Object.keys(items).length > 0" class="table table-bordered">
                    <thead>
                    <tr>
                        <th v-for="field in fields">{{field.title}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td v-for="(field, key) in fields"><input @change="updateField($event, item['id'], key)"
                                                                  v-bind:value="item[key]" :disabled="field.disabled">
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
            search(value) {
                let self = this;
                Data.vendors.getVendorsList().then(function(all){

                    let result = [];

                    for (let i = 0; i < all.length; i++) {
                        if (all[i]['id'] == value || all[i]['title'].indexOf(value) != -1) {
                            result.push(all[i]);
                        }
                    }

                    self.$set(self, 'items', result);
                });
            }
        }
    });
</script>