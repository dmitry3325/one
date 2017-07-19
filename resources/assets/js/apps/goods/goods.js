import items_list from './components/items_list.vue';

new Vue({
    el: '#content',
    template: require('./tpls/main.html'),
    data: {
        tabs: {
            'sections' : {
                'title' : 'Разделы',
                'isActive' : false,
                'isBase' : true,
            },
            'filters' : {
                'title' : 'Фильтры',
                'isActive' : false,
                'isBase' : true,
            },
            'goods' : {
                'title' : 'Товары',
                'isActive' : false,
                'isBase' : true,
            },
            'seo' : {
                'title' : 'HTML-страницы',
                'isActive' : false,
                'isBase' : true,
            }
        }
    },
    created : function () {
        this.fillBaseData();
    },
    mounted: function () {
        /*var self = this;
        setTimeout(function () {
            var child = new Child({
                el: self.$el,
                parent: self,
                data: {
                    'my_var': 2
                }
            });
        }, 1000);*/
    },
    methods: {
        fillBaseData : function(){
            let activeTab = Url.get('tab');
            if(!activeTab || !this.tabs[activeTab]){
                activeTab = 'sections';
            }
            this.tabs[activeTab].isActive = true;
            this.activeTab = activeTab;
            this.showContent();
        },
        switchCategory : function (selected) {
            for(let tab in this.tabs){
                if(tab === selected) this.tabs[tab].isActive = true;
                else this.tabs[tab].isActive = false;
            }
            this.activeTab = selected;
            Url.set('tab', selected);
            this.showContent();
        },
        showContent : function(){
            //console.log(this.activeTab)
        },
        makeSearch: function(e){
            this.tabs['search'] = {
                'title': 'Поиск',
                'isActive' : false
            };
            this.curSearch = e.target.value;
            e.target.value = '';
            this.$forceUpdate();
            this.switchCategory('search');
        },
        removeTab: function(tab){
            delete this.tabs[tab];
            Url.unset('tab');
            this.$forceUpdate();
        }
    },
});

