
export default {
    install(Vue){
        window.Data = Vue.Data = {
            'entity' : require('./data/entity.js'),
        };
    }
}