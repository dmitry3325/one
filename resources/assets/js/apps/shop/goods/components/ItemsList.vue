<template>
    <div class="item-list">
        <div class="list">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <div v-if="Object.keys(items).length > 0">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th v-for="field in fields">{{(fields_info[field]) ? fields_info[field].title : field}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="row-item" v-for="item in items">
                            <td>
                                <button @click="showEntity(item['id'])" type="button"
                                        class="btn btn-sm btn-default wh-100 cursor-pointer">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </button>
                            </td>
                            <td v-for="field in fields">{{prepareField(item[field], field)}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <pager :total="total" curPage="curPage" :change="changePage" align="center"></pager>
                </div>
                <div v-else class="text-center"><h3>Ничего не нашлось</h3></div>
            </div>
        </div>
    </div>
</template>
<script>
    let Pager = require('../../../../components/pager.vue');

    module.exports = Vue.extend({
        components: {
            'pager': Pager
        },
        data: function () {
            return {
                'entity': null,
                'loading': true,
                'fields_info': {},
                'items': {},
                'fields': {},
                'section': null,
                'filters': {},
                'total': 0,
                'curPage': 0
            }
        },
        mounted: function () {
            if (!this.curPage) {
                if (Url.get('curPage')) {
                    this.curPage = Url.get('curPage');
                } else this.curPage = 1;
            }
            Url.set('page', this.curPage);
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
                        'section_id': this.section,
                        'paginate': true,
                        'current_page': this.curPage,
                        'order_by': {
                            'field': 'id',
                            'type': 'desc'
                        }
                    }, true)
                ];

                if (!Object.keys(self.fields).length) {
                    q.push(Data.entity.getBaseFields(this.entity));
                }

                return Ajax.when(q, function (fields, items, fields_list) {
                    self.loading = false;
                    self.fields_info = fields;
                    if (items.data) {
                        self.$set(self, 'items', items.data);
                    }

                    if (items.last_page) {
                        self.$set(self, 'total', items.last_page);
                    }

                    if (fields_list) {
                        self.fields = fields_list;
                    }
                });

            },
            prepareField(value, key) {
                return value;
            },
            showEntity(id) {
                window.open(location.pathname + '?entity=' + this.entity + '&id=' + id, '_blank');
            },
            changePage(page) {
                this.curPage = page;
                Url.set('page', this.curPage);
                this.loadData();
            }
        }
    });
</script>
<style>
    .item-list .row-item {
        cursor: pointer;
    }

    .item-list thead {
        font-size: 12px;
    }

    .item-list thead th {
        white-space: nowrap;
    }
</style>