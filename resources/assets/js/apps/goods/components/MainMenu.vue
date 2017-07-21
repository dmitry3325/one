<template>
    <div class="menu">
        <ul class="nav nav-tabs">
            <li v-for="(tab,index) in tabs" class="nav-item">
                <a class="nav-link" :class="{active: tab.isActive}" href="" @click.prevent="switchCategory(index)">
                    <span class="title text-ellipsis">{{tab.title}}</span>
                    <span v-show="tab.isBase !== true" @click.prevent="removeTab(index)"
                          class="glyphicon glyphicon-remove"></span>
                </a>
            </li>
            <div class="ml-auto">
                <input class="form-control" placeholder="Поиск..." @change="showSearch($event.target.value,$event)"
                       type="text">
            </div>
        </ul>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                tabs: {
                    'sections': {
                        'title': 'Разделы',
                        'isActive': false,
                        'isBase': true,
                    },
                    'filters': {
                        'title': 'Фильтры',
                        'isActive': false,
                        'isBase': true,
                    },
                    'goods': {
                        'title': 'Товары',
                        'isActive': false,
                        'isBase': true,
                    },
                    'seo': {
                        'title': 'HTML-страницы',
                        'isActive': false,
                        'isBase': true,
                    }
                },
                fields: {},
                filters: {},
            }
        },
        mounted: function () {
            let activeTab = Url.get('tab');

            if (Url.get('search')) {
                this.showSearch(Url.get('search'));
            }

            if (!activeTab || !this.tabs[activeTab]) {
                activeTab = 'sections';
            }
            this.tabs[activeTab].isActive = true;
            this.activeTab = activeTab;

            this.switchCategory(this.activeTab);
        },
        methods: {
            switchCategory: function (selected) {
                for (let tab in this.tabs) {
                    if (tab === selected) this.tabs[tab].isActive = true;
                    else this.tabs[tab].isActive = false;
                }
                this.activeTab = selected;
                Url.set('tab', selected);
                this.showContent();
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
                delete this.tabs[tab];
                if (tab === 'search') {
                    Url.unset('search');
                }
                Url.unset('tab');
                this.$forceUpdate();
            },
            showContent: function () {
                if (typeof this.action === 'function') {
                    this.action(this.activeTab, this.fields, this.filters);
                }
            }
        }
    });
</script>