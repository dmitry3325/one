import ItemsList from './components/ItemsList.vue';
import MainMenu from './components/MainMenu.vue';

new Vue({
    el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
        filters: {},
        fields: {},
    },
    beforeCreate: function () {
    },
    mounted: function () {
        this.setMainParams();
        this.buildApp();
    },
    methods: {
        setMainParams(){

        },
        buildApp(){
            this.Menu = new MainMenu({
                el: '.menu',
                data: {
                    action: this.showList
                }
            });
        },
        showList(tab){
            if (!this.$content) {
                this.$content = document.querySelector('.main-content');
            }

            this.$content.innerHTML = '';
            let $el = this.setTarget(this.$content);

            new ItemsList({
                el: $el,
                data: {
                    entity: tab.entity,
                    filters: this.filters[tab.entity],
                    fields: this.fields[tab.entity],
                    selected: {
                        'title': ',kzkzz'
                    },
                }
            });
        }
    }
});

