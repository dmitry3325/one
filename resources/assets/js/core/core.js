import './bootstrap';
import BS from "bootstrap.native";
import Vue from "vue";
import Ajax from "../classes/ajax.js";
import Errors from "../classes/errors.js";
import Url from '../classes/url.js';
import Cookies from '../classes/cookies.js';
import Ls from '../classes/ls.js';
import Data from '../classes/data.js';

Vue.use(Url);
Vue.use(Ajax);
Vue.use(Cookies);
Vue.use(Ls);
Vue.use(Data);
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
window.BS = BS;
window.Errors = Errors;