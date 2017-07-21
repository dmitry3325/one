import ItemsList from './components/items_list.vue';
import MainMenu from './components/MainMenu.vue';

new Vue({
    el: '#content',
    replace: true,
    template: require('./tpls/main.html'),
    data: {
        filters: {}
    },
    beforeCreate: function () {

    },
    mounted: function () {
        this.buildApp();
    },

    methods: {
        buildApp(){
            new MainMenu({
                el: '.menu',
                parent: this,
                data: {
                    action : this.showList
                }
            });
        },
        showList(tab, fields , filters){
            console.log(tab, fields , filters);
        }
    }
});

