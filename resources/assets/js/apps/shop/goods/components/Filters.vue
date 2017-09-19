<template>
    <div class="filters">
        <div v-if="loading">Подождите, данные загружаются...</div>
        <div v-else>
            <div v-if="entity == 'Sections'">
                <div v-for="filter in filters" class="row">
                    <div class="col-md-3">
                        <span class="badge badge-default">
                            {{((typeof filter.code !== 'undefined') ? 'Фильтр №' : 'Новый фильтр №') + filter.num}}
                        </span>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" v-model="filter.value"
                               placeholder="Введите значение фильтра">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn w-100"
                                :class="(filter.auto_create)?'btn-success':'btn-secondary'"
                                @click="filter.auto_create = !filter.auto_create">
                            <span class="glyphicon"
                                  :class="(filter.auto_create)?'glyphicon-check':'glyphicon-unchecked'"></span>&nbsp;
                            Авто-создание
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button v-if="typeof filter.code!=='undefined'" @click="deleteFilter(filter.num)"
                                class="btn w-100 btn-danger"><span class="glyphicon glyphicon-remove"></span> Удалить
                        </button>
                        <button v-else-if="!filter.code" @click="addFilter()"
                                class="btn w-100 btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить
                        </button>
                    </div>
                    <div class="col-md-12">
                        <hr/>
                    </div>
                </div>
            </div>
            <div v-else>
                <div v-for="sf in sectionFilters" class="row">
                    <div class="col-md-3">
                        <span class="badge badge-default">
                            {{sf.value}}
                        </span>
                    </div>
                    <div class="col-md-9">
                        <div v-for="filter in filters" v-if="filter.num === sf.num" class="row mb-2">
                            <div class="col-md-6">
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
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="ID после сохранения" disabled
                                       v-model="filter.code">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
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
                filters: {},
                sectionFilters: {},
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
            Vue.Events.on('entity_save', this.save);
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
                            resolve();
                        }
                    });
                });
            },
            loadFilters() {
                let self = this;
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
                Ajax.post('/shop', 'saveFilters', {
                    'entity': this.entity,
                    'id': this.id,
                    'filters': this.filters
                }).then(function () {
                    self.loadFilters();
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
</style>