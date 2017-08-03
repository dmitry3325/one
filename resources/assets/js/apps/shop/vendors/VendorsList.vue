<template>
    <div>
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
                        <td v-for="field in fields"><input @change="updateField($event, item['id'], field)"
                                                           v-bind:value="item[field]" :disabled="fields_info[field].disabled"></td>
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
                'fields_info': {},
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

                    Ajax.post('/shop/vendors', 'getVendorsList', {
                        filters: this.filters,
                        fields: this.fields
                    })
                ];


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
            updateField(event, id, field) {
                let options = {};
                options[field] = event.target.value;

                force = event.target.value !== event.target._value;
                if(force) {
                    Data.vendors.update(id, options, force);
                }
            }
        }
    });
</script>