require('./bootstrap');
window.Vue = require('vue');
Vue.use(require('vue-resource'));
require('./core/core.js');


Ajax.post('goods','getGoods1',{'id':1},function(data){console.log(data)});



const tpl = require('./core/tpl');
const app = new Vue({
    el: '#content',
    mixins: [tpl],
    mounted: function () {
        this.hello();
    }
});
