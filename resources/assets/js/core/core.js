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

window.Vue = Vue;
window.Errors = Errors;