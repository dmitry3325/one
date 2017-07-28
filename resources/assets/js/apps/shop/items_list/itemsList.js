import ItemsList from './components/ItemsList.vue';
import MainMenu from './components/MainMenu.vue';

new Vue({
    el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
        first_show : true,
        comps: [],
    },
    beforeCreate: function () {
    },
    mounted: function () {
        this.setMainParams();
        this.buildApp();
    },
    methods: {
        setMainParams() {

        },
        buildApp() {
            this.Menu = new MainMenu({
                el: '.menu',
                data: {
                    action: this.showList
                }
            });
        },
        showList(tab) {
            if (!this.$content) {
                this.$content = document.querySelector('.main-content');
            }

            let filters = {};
            if(this.first_show){
                filters = Url.get('filters');
            }

            this.$content.innerHTML = '';
            let $el = this.setTarget(this.$content);
            if (tab.entity) {
                let L = new ItemsList({
                    el: $el,
                    data: {
                        entity: tab.entity,
                        filters : filters,
                        selected: {
                            'title': ',kzkzz'
                        },
                    }
                });

                if(this.first_show && filters && Object.keys(filters).length) {
                    Ls.set(L._getFiltersKey(),filters);
                    Ls.get(L._getFiltersKey())
                }
            }

            this.first_show = false;
        }
    }
});

