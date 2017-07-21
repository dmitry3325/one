import ItemsList from './components/items_list.vue';

new Vue({
    //el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
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
        filters: {}
    },
    beforeCreate: function () {
        this.setTarget('#content');
    },
    mounted: function () {
        let activeTab = Url.get('tab');
        if (!activeTab || !this.tabs[activeTab]) {
            activeTab = 'sections';
        }
        this.tabs[activeTab].isActive = true;
        this.activeTab = activeTab;

        if (Url.get('search')) {
            this.showSearch(Url.get('search'));
        }

        this.showContent();
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
            }
            this.switchCategory('search');
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

            switch (this.activeTab) {
                case 'sections':
                    break;
                case 'filters':
                    break;
                case 'goods':
                    break;
            }

            var data = {};


            new ItemsList({
                el: '.main-content',
                parent: this,
                data: data
            });
        },
    },
});

