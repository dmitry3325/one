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
                        <th></th>
                        <th v-for="field in fields">{{(fields_info[field]) ? fields_info[field].title : field}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="row-item" v-for="item in items">
                        <td>
                            <button type="button" class="btn btn-sm btn-default wh-100 cursor-pointer"><span
                                    class="glyphicon glyphicon-edit"></span></button>
                        </td>
                        <td v-for="field in fields">{{prepareField(item[field], field)}}</td>
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
                'fields': {},
                'section': null,
                'filters': {},
            }
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            loadData() {
                let self = this;
                let q = [
                    Data.entity.getAllFields(this.entity),
                    Data.entity.getItemsList(this.entity, {
                        'filters': this.filters,
                        'fields': this.fields,
                        'section_id': this.section
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
            prepareField(value, key) {
                return value;
            }
        }
    });
</script>
<style>
    .row-item {
        cursor: pointer;
    }
    thead{
        font-size:12px;
    }
    thead th{
        white-space: nowrap;
    }
</style>