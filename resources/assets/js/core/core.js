import './bootstrap';
import Vue from "vue";
import Ajax from "./ajax.js";
import Errors from "./errors.js";

window.Vue = Vue;
window.Ajax = window.J = Ajax;
window.Errors = Errors;