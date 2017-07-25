import './bootstrap';
import Vue from "vue";
import Ajax from "./ajax.js";
import Errors from "./errors.js";
import Url from './url.js';
import Cookies from './cookies.js';
import Ls from './ls.js';

Vue.use(Url);
Vue.use(Ajax);
Vue.use(Cookies);
Vue.use(Ls);
Vue.use({
    install(Vue){
        Vue.prototype.setTarget = function (el, set) {
            if(typeof el === "string"){
                el = document.querySelector(el);
            }
            if(!el){
                console.log('Warning! Target element not found!');
                return false;
            }
            var vueEl = document.createElement('div');
            el.appendChild(vueEl);
            if(set === true) this.$options.el = vueEl;
            return vueEl;
        }
    }
});

window.Vue = Vue;
window.Errors = Errors;