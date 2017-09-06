<template>
    <div>
        <div class="menu">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="" @click.prevent="((typeof showMenu === 'function')?showMenu():false)">
                        <span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                    </a>
                </li>
                <li v-for="(tab,index) in tabs" class="nav-item">
                    <a class="nav-link" :class="{active: index == activeTab}" href=""
                       @click.prevent="switchCategory(index)">
                        <span class="title text-ellipsis">{{tab.title}}</span>
                        <span v-show="tab.isBase !== true" @click.prevent="removeTab(index)"
                              class="glyphicon glyphicon-remove"></span>
                    </a>
                </li>
                <div class="d-flex ml-auto">
                    <div class="ml-3">
                        <input class="form-control" placeholder="Поиск..."
                               @change="showSearch($event.target.value,$event)"
                               type="text">
                    </div>
                </div>
            </ul>
        </div>
        <div class="sections-list">
            <div v-if="_getContType() === 'ItemsList'" class="tool-bar">
                <ol class="breadcrumb d-flex">
                    <div class="ml-3 ml-auto">
                        <button type="button" class="btn btn-info" @click="showFiltersSelector">Добавить фильтр</button>
                    </div>
                    <div class="ml-3">
                        <button type="button" class="btn btn-warning" @click="showFieldsEditor">Настроить поля</button>
                    </div>
                    <div class="ml-3">
                        <button type="button" @click="loadFile" class="btn btn-primary">
                            <span class="glyphicon glyphicon-arrow-down"></span>
                        </button>
                    </div>
                    <div class="ml-3">
                        <button type="button" class="btn btn-success" @click="createEntity">Создать</button>
                    </div>
                </ol>
            </div>
            <div class="tab-content">
                <div v-for="(tab,index) in tabs" v-show="index === activeTab" class="tab"
                     :class="((index === activeTab)?'active':'')+' '+index">
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    let FilterSelector = require('../../../../components/filtersSelector.vue');
    let FieldsSelector = require('../../../../components/fieldsSelector.vue');
    let ItemsList = require('./ItemsList.vue');
    let EntityEdit = require('./EntityEdit.vue');

    module.exports = Vue.extend({
        data: function () {
            return {
                id: null,
                filters: [],
                activeTab: 'goods',
                search: '',
                list: [],
                tabs: {
                    'goods': {
                        'title': 'Товары',
                        'isBase': true,
                        'entity': 'Goods',
                        'type': 'ItemsList',
                    },
                    'filters': {
                        'title': 'Фильтры',
                        'isBase': true,
                        'entity': 'Filters',
                        'type': 'ItemsList',
                    },
                    'sections': {
                        'title': 'Редактировать раздел',
                        'isBase': true,
                        'entity': 'Sections',
                        'type': 'EntityEdit',
                    }
                }
            }
        },
        mounted: function () {
            let activeTab = Url.get('tab');

            if (Url.get('search')) {
                this.showSearch(Url.get('search'));
            }

            if (Url.get('filter')) {
                this.$set(this, 'filters', Url.get('filter'));
            } else if (Ls.get('selected_filters')) {
                this.$set(this, 'filters', Ls.get('selected_filters'));
            }

            if (!activeTab || !this.tabs[activeTab]) {
                activeTab = 'goods';
            }
            this.activeTab = activeTab;
            this.switchCategory(this.activeTab);
        },
        methods: {
            switchCategory: function (selected) {
                this.activeTab = selected;
                Url.set('tab', selected);
                this.showContent();
            },
            showContent: function () {
                let tab = this.tabs[this.activeTab];
                if (tab.builded) return;

                let $target = document.querySelector('.tab-content .tab.' + this.activeTab);
                $target.innerHTML = '';
                let contType = this._getContType();

                if (contType === 'ItemsList') {
                    let fields = Ls.get(this._getCurEntity() + 'fields');
                    this.tabs[this.activeTab].view = new ItemsList({
                        el: this.setTarget($target),
                        data: {
                            entity: this._getCurEntity(),
                            section: this.id,
                            filters: Funs.cloneObject(this.filters),
                            fields: (fields) ? fields : []
                        },
                    });
                } else if (contType === 'EntityEdit') {
                    let $el = this.setTarget($target);
                    let $cont = this.setTarget($el);
                    $el.classList.add('mt-3');
                    this.tabs[this.activeTab].view = new EntityEdit({
                        el: $cont,
                        propsData: {
                            id: this.id,
                            entity: this._getCurEntity(),
                        },
                    });
                }

                tab.builded = true;
            },
            showSearch: function (value, e) {
                this.tabs['search'] = {
                    'title': 'Поиск',
                    'isActive': false
                };
                this.curSearch = value;
                Url.set('search', value);
                if (e) {
                    e.target.value = '';
                    this.switchCategory('search');
                }
                this.$forceUpdate();
            },
            removeTab: function (tab) {
                this.$delete(this.tabs, tab);

                if (tab === 'search') {
                    Url.unset('search');
                }
                Url.unset('tab');
            },
            createEntity(e) {
                let self = this;
                let entity = this._getCurEntity(true);
                if (entity && Data[entity]) {
                    Data[entity].create(this._getCurEntity(), {'section_id': this.id})
                        .then(function (res) {
                            if (res.id) {
                                window.open(location.pathname + '?entity=' + self._getCurEntity() + '&id=' + res.id, '_blank');
                            }
                            self.callLoadData(function () {
                                AppNotifications.add({
                                    'type': 'success',
                                    'body': 'Готово! Создано успешно!'
                                });
                            });
                        });
                }
            },
            callLoadData(callback, setData) {
                if (typeof setData === 'undefined') setData = {};
                if (this.tabs[this.activeTab].view) {
                    let module = this.tabs[this.activeTab].view;
                    for (let i in setData) {
                        module.$set(module, i, setData[i]);
                    }
                    if (module.loadData) {
                        let def = module.loadData();
                        if (def.then && typeof callback === 'function') {
                            def.then(function () {
                                callback();
                            });
                        }
                    }
                }
            },
            loadFile(e) {
                Ajax.getFile('/shop', 'loadFile', {
                    'entity': this._getCurEntity(), 'options': {
                        'filters': this.filters,
                        'fields': Ls.get(this._getCurEntity() + 'fields'),
                        'section_id': this.id,
                    }
                })
            },
            showFiltersSelector() {
                let self = this;
                new FilterSelector({
                    el: this.setTarget('#goodsAdmin'),
                    data: {
                        'entity': this._getCurEntity(),
                        'filters': ((this.filters) ? this.filters : []),
                        'callback': function (filters) {
                            self.filters = filters;
                            Url.set('filters', filters);
                            Ls.set('selected_filters', filters);
                            self.callLoadData(null, {
                                'filters': filters
                            });
                        }
                    }
                });
            },
            showFieldsEditor(e) {
                let self = this;

                let fields = Ls.get(this._getCurEntity() + 'fields');
                new FieldsSelector({
                    el: this.setTarget('#goodsAdmin'),
                    data: {
                        'entity': this._getCurEntity(),
                        'fields': ((fields) ? fields.slice() : []),
                        'callback': function (fields) {
                            Ls.set(self._getCurEntity() + 'fields', fields);
                            self.callLoadData(null, {
                                'fields': fields
                            });
                        }
                    }
                });
            },
            _getCurEntity(get_data_key) {
                if (get_data_key) return (this.tabs[this.activeTab]) ? this.activeTab : null;
                else return (this.tabs[this.activeTab]) ? this.tabs[this.activeTab].entity : null;
            },
            _getContType() {
                if (this.activeTab && this.tabs[this.activeTab] && this.tabs[this.activeTab].type) {
                    return this.tabs[this.activeTab].type
                } else {
                    return null;
                }
            }
        }
    });
</script>
<style>
    .nav-item .nav-link .glyphicon {
        right: -8px;
    }

    .glyphicon-arrow-down {
        top: 3px
    }
</style>