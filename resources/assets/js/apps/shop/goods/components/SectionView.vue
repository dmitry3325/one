<template>
    <div>
        <div class="menu">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="" @click.prevent="((typeof showMenu === 'function')?showMenu():false)">
                        <span class="glyphicon glyphicon-th-large"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                    </a>
                </li>
                <li v-for="(tab,index) in tabs" v-if="!tab.hidden" class="nav-item">
                    <a class="nav-link" :class="{active: index == activeTab}" href=""
                       @click.prevent="switchCategory(index)">
                        <span class="title text-ellipsis">{{tab.title}}</span>
                        <span v-show="tab.isBase !== true" @click.prevent="hideTab(index)"
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
        <div id="filterModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">Выберите фильтры</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <filter-selector :entity="_getCurEntity()" :callback="filterSelectorCallback"></filter-selector>
                    </div>
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
    let Search = require('./Search.vue');

    module.exports = Vue.extend({
        components: {
            'filter-selector': FilterSelector
        },
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
                    },
                    'search': {
                        'title': 'Поиск',
                        'isBase': false,
                        'type': 'Search',
                        'hidden' : true
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
                }else if(contType === 'Search'){
                    let $el = this.setTarget($target);
                    $el.classList.add('mt-3');
                    let $cont = this.setTarget($el);
                    this.tabs[this.activeTab].view = new Search({
                        el: $cont,
                        data: {
                            section: this.id,
                            search: this.curSearch,
                        },
                    });
                }

                tab.builded = true;
            },
            showSearch: function (value, e) {
                this.$set(this.tabs['search'], 'hidden', false);
                this.curSearch = value;
                Url.set('search', value);
                if(this.tabs['search'].view){
                    this.tabs['search'].view.makeSearch(value);
                }

                if (e) {
                    e.target.value = '';
                    this.switchCategory('search');
                }
            },
            hideTab: function (tab) {
                this.$set(this.tabs[tab], 'hidden', true);
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
                $('#filterModal').modal('show');
            },
            filterSelectorCallback(filters){
                this.filters = filters;
                Url.set('filters', filters);
                Ls.set('selected_filters', filters);
                this.callLoadData(null, {
                    'filters': filters
                });
                $('#filterModal').modal('hide');
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