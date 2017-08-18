import './bootstrap';
import './extentions/extentions.js';
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
            let vueEl = document.createElement('div');
            el.appendChild(vueEl);
            if(set === true) this.$options.el = vueEl;
            return vueEl;
        };

        Vue.prototype.call = function (func) {
            if (typeof this[func] === 'function') {
                this[func]();
            }
        };

        Vue.prototype.cloneObject = function (obj) {
            let clone = {};
            for(let i in obj) {
                if(typeof(obj[i])==="object" && obj[i] !== null)
                    clone[i] = this.cloneObject(obj[i]);
                else
                    clone[i] = obj[i];
            }
            return clone;
        };
    }
});

window.Vue = Vue;
window.Errors = Errors;
window.AppNotifications = new Vue(require('../components/notifications.vue'));
