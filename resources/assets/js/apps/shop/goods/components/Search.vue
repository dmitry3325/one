<template>
    <div>
        <div v-if="inProcess || !fieldsLoaded" class="text-center">
            <h2 v-if="search">Подождите идет поиск...</h2>
        </div>
        <div v-else>
            <div><h2>Вы искали: {{search}}</h2></div>
            <div class="mt-1">
                <div v-for="(data,tab) in result" class="card mb-3">
                    <div class="card-header" :id="'heading'+tab">
                        <h5 class="mb-0">
                            <a class="btn btn-link" href="#" data-toggle="collapse" :data-target="'#collapse'+tab"
                               aria-expanded="true" :aria-controls="'collapse'+tab">
                                {{(entities[tab])?entities[tab]:tab}} ({{Object.keys(data.data).length}})
                            </a>
                        </h5>
                    </div>

                    <div :id="'collapse'+tab" :class="'collapse ' + ((tab == 'Goods') ? 'show' : '')" :aria-labelledby="'heading'+tab">
                        <div class="card-body">
                            <div v-if="Object.keys(data.data).length > 0">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="edit-button-cont"></th>
                                        <th v-for="(val, field) in data.data[0]">
                                            {{(fields[field]) ? fields[field].title : field}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="row-item" v-for="item in data.data">
                                        <td class="edit-button-cont">
                                            <button @click="showEntity(tab,item['id'])" type="button"
                                                    class="btn btn-sm btn-default wh-100 cursor-pointer">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </button>
                                        </td>
                                        <td v-for="(val,field) in item">{{val}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <pager :total="data.last_page" curPage="pages[tab]" :alias="tab" :change="changePage" align="center"></pager>
                            </div>
                            <div v-else class="text-center"><h3>Ничего не нашлось</h3></div>
                        </div>
                    </div>
                </div>
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
                'section': null,
                'search': null,
                'inProcess': true,
                'query': null,
                'result': {},
                'fields': {},
                'fieldsLoaded': false,
                'entities': {
                    'Goods': 'Товары',
                    'Sections': 'Категории',
                    'Filters': 'Фильтры',
                    'HtmlPages': 'Страницы',
                },
                'pages': {},
            }
        },
        mounted: function () {

            let qBaseFields = [];
            for (let entity in this.entities) {
                qBaseFields.push(Data.entity.getAllFields(entity))
            }
            let self = this;
            Ajax.when(qBaseFields).then(function (result) {
                for (let i in self.entities) {
                    self.$set(self.fields, self.entities[i], result[i]);
                }
                self.$set(self, 'fieldsLoaded', true);
            });

            if (this.search) {
                this.makeSearch(this.search);
            }
        },
        methods: {
            makeSearch(value, params, callback) {
                if(typeof params !== 'object') {
                    params = {};
                }

                let self = this;
                this.search = value;

                if(this.section){
                    params['section'] = this.section;
                }

                this.query = Ajax.post('/shop', 'search', {
                    'search': this.search,
                    'params': params
                }).then(function (result) {
                    for(let entity in result) {
                        self.$set(self.result, entity, result[entity]);
                    }
                    self.inProcess = false;
                    if(typeof callback === 'function'){
                        callback();
                    }
                });
            },
            showEntity(entity, id) {
                window.open(location.pathname + '?entity=' + entity + '&id=' + id, '_blank');
            },
            changePage(page, entity){
                this.makeSearch(this.search, {
                    'page': page,
                    'entities': [entity]
                });
            }
        }
    });
</script>