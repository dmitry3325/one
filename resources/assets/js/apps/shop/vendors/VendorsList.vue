<template>
    <div>
        <h1>hello</h1>

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
        },
        methods: {
            loadData() {
                let self = this;

                let q = [
                    Data.vendors.getAllFields(),

                Ajax.post('/shop/vendors', 'getVendorsList', {
                    entity: this.entity,
                    filters: this.filters,
                    fields: this.fields
                })];


                if (!Object.keys(self.fields).length) {
                    q.push(Data.vendors.getBaseFields());
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
            prepareField(value, key) {
                return value;
            },
        }
    });
</script>