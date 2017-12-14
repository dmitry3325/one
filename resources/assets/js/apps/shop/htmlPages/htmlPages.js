import HtmlPages from './htmlPages.vue';

new Vue({
    el: '#content',
    template: require('./tpls/main.html'),
    data: {},
    mounted: function () {
        this.buildApp();
    },
    methods: {
        buildApp() {
            this.htmlPages = new HtmlPages({
                el: '.main-content',
                data: {
                    action: this.showList
                }
            });
        },
        showList() {
            if (!this.$content) {
                this.$content = document.querySelector('.main-content');
            }

            this.$content.innerHTML = '';
            let $el = this.setTarget(this.$content);
        }
    }
});

