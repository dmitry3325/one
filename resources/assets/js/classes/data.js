export default {
    install(Vue) {
        window.Data = Vue.Data = {};
        let Entity = require('./data/entity.js');

        for(let key in Entity){
            Data[key] = Entity[key];
        }

        Data['vendors'] = require('./data/vendors.js');
        Data['htmlPages'] = require('./data/htmlPages.js');

    }
}