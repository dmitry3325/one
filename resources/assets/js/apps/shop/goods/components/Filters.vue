<template>
    <div class="filters">
        <div v-if="loading">Подождите, данные загружаются...</div>
        <div v-else>
            <div v-if="entity == 'Sections'">
                <div v-for="filter in filters" class="row">
                    <div class="col-sm-2">
                        <span class="badge badge-default">
                            {{((typeof filter.code !== 'undefined') ? 'Фильтр №' : 'Новый фильтр №') + filter.num}}
                        </span>
                    </div>
                    <div class="col-sm-4">
                        <div v-if="!filter.code" class="input-group mb-3">
                            <input type="text" class="form-control" v-model="filter.value"
                                   placeholder="Введите значение фильтра">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="pull-right dropdown-menu">
                                    <a v-for="df in defaultFilters" class="dropdown-item" @click="filter.value = df" href="#">{{ df }}</a>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <input type="text" class="form-control" v-model="filter.value"
                                   placeholder="Введите значение фильтра">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="text" class="form-control" v-model="filter.order_by"
                                       placeholder="Очередь вывода">
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn w-100"
                                        :class="(filter.hidden)?'':'btn-secondary'"
                                        @click="filter.hidden = !filter.hidden">
                                    <span class="glyphicon"
                                          :class="(filter.hidden)?'glyphicon-check':'glyphicon-unchecked'"></span>&nbsp;
                                    Скрыт
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn w-100"
                                :class="(filter.auto_create)?'btn-success':'btn-secondary'"
                                @click="filter.auto_create = !filter.auto_create">
                            <span class="glyphicon"
                                  :class="(filter.auto_create)?'glyphicon-check':'glyphicon-unchecked'"></span>&nbsp;
                            Авто-создание
                        </button>
                    </div>
                    <div class="col-sm-2">
                        <button v-if="typeof filter.code!=='undefined'" @click="deleteFilter(filter.num)"
                                class="btn w-100 btn-danger"><span class="glyphicon glyphicon-remove"></span> Удалить
                        </button>
                        <button v-else-if="!filter.code" @click="addFilter()"
                                class="btn w-100 btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить
                        </button>
                    </div>
                    <div class="col-sm-12">
                        <hr/>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-lg btn-warning float-left" @click="generateFilters">Сгенерировать фильтры</button>
                        <div class="float-left field-description ml-4">
                            Фильтры создаются в зависимости от товаров, которые доступны в этом разделе.<br>
                            Создание фильтров происходит автоматически каждую ночь.
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div v-for="sf in sectionFilters" class="row">
                    <div class="col-sm-3">
                        <span class="badge badge-default">
                            {{sf.value}}
                        </span>
                    </div>
                    <div class="col-sm-9">
                        <div v-for="filter in filters" v-if="filter.num === sf.num" class="row mb-2">
                            <div class="col-sm-6">
                                <div class="input-group w-100">
                                    <input type="text" v-model="filter.value"
                                           class="form-control border-right-0 dropdown-toggle" data-toggle="dropdown">
                                    <div v-if="sf.distinct_values" class="dropdown-menu">
                                        <a v-for="distF in sf.distinct_values" class="dropdown-item"
                                           v-show="!entity_filter_distinct[filter.num] || entity_filter_distinct[filter.num].indexOf(distF.value) == -1"
                                           href="#" @click="$set(filter,'value',distF.value)"
                                           :class="((distF.value == filter.value)?'active':'')">
                                            {{distF.value}}
                                        </a>
                                    </div>
                                    <span class="input-group-btn">
                                        <button v-if="filter.code" @click="deleteFilter(sf.num, filter.value)"
                                                class="btn btn-danger">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </button>
                                        <button v-else-if="!filter.code" @click="addFilter({num:sf.num})"
                                                class="btn btn-success">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" placeholder="ID после сохранения" disabled
                                       v-model="filter.code">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <hr/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        props: {
            entity: String,
            id: Number
        },
        data: function () {
            return {
                allowSave : false,
                filters: {},
                sectionFilters: {},
                defaultFilters: {},
                Model: {},
                loading: true,
                entity_filter_distinct: {},
            }
        },
        mounted: function () {
            let self = this;
            this.getData().then(function () {
                self.loadFilters().then(function () {
                    self.loading = false;
                });
            });
            Vue.Events.on('Entity:after_save', this.save);
        },
        methods: {
            getData() {
                let self = this;
                return new Promise((resolve) => {
                    Data.entity.get(this.entity, this.id).then(function (model) {
                        self.Model = model;
                        if (self.entity !== 'Sections' && model.section_id) {
                            Ajax.post('/shop', 'getSectionFilters', {
                                'id': model.section_id
                            }).then(function (res) {
                                self.sectionFilters = res;
                                resolve();
                            });
                        } else {
                            Ajax.post('/shop', 'getDefaultEntityFilters').then(function(res){
                                self.defaultFilters = res;
                            });
                            resolve();
                        }
                    });
                });
            },
            loadFilters() {
                let self = this;
                self.allowSave = false;
                return Ajax.post('/shop', 'getEntityFilters', {
                    'entity': this.entity,
                    'id': this.id
                }).then(function (res) {
                    self.filters = res;

                    self.entity_filter_distinct = {};
                    self.filters.forEach(function (v) {
                        if (!self.entity_filter_distinct[v.num]) {
                            self.entity_filter_distinct[v.num] = [];
                        }
                        self.entity_filter_distinct[v.num].push(v.value);
                    });

                    if (self.entity === 'Sections') {
                        self.addFilter();
                    } else {
                        self.sectionFilters.forEach(function (obj) {
                            self.addFilter({
                                num: obj.num,
                            });
                        });
                    }
                    self.allowSave = true;
                });
            },
            addFilter(filter, e) {
                if (e) {
                    e.target.closest('.btn-group').querySelector('input').value = '';
                }
                if (this.entity === 'Sections') {
                    let num = 0;
                    for (let i in this.filters) num = this.filters[i].num;
                    num++;
                    let fil = {
                        num: num,
                        value: ''
                    };
                    if (this.entity === 'Sections') {
                        fil.auto_create = true;
                    }

                    this.filters.push(fil);
                } else {
                    this.filters.push(Funs.cloneObject(filter));
                }
            },
            deleteFilter(num, value) {
                for (let i = this.filters.length - 1; i >= 0; i--) {
                    let fil = this.filters[i];
                    if (fil.num === num && (typeof value === 'undefined' || value === fil.value)) {
                        this.filters.splice(i, 1);
                    }
                }
            },
            save() {
                let self = this;
                if(!self.allowSave) return null;
                self.allowSave = false;
                Ajax.post('/shop', 'saveFilters', {
                    'entity': this.entity,
                    'id': this.id,
                    'filters': this.filters
                }).then(function () {
                    self.loadFilters();
                });
            },
            generateFilters(e){
                if(this.entity !== 'Sections') return;
                e.target.innerHTML = '<span class="glyphicon glyphicon-refresh"></span> Ожидайте...';
                Ajax.post('/shop','generateFilters', {'section_id': this.id}).then(function(res){

                });
            }
        }
    });
</script>
<style>
    .filters .badge {
        font-size: 18px;
        width: 100%;
        padding: 10px 15px;
    }

    .filters .dropdown-toggle::after {
        margin-left: 0;
    }
    .field-description{
        color: #888;
    }
</style>