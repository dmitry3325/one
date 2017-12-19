class Vendors {
    constructor() {
        this.allFields = null;
        this.all = null;
    }

    getAllFields(force) {
        let self = this;
        if (self.allFields && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.allFields);
            });
        } else {
            return Ajax.post('/shop/htmlPages', 'getAllFields', {}, function (data) {
                self.allFields = data.data;
            });
        }
    }

    getHtmlPagesList(options, force) {
        let self = this;
        if (self.all && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.all);
            });
        } else {
            return Ajax.post('/shop/htmlPages', 'getHtmlPagesList', {options}, function (data) {
                if (data.result && data.data) {
                    self.all = data.data;
                }
            });
        }
    }

    update(id, data) {
        return Ajax.post('/shop/htmlPages', 'update', {id: id, data:data});
    }

    delete(id) {
        return Ajax.post('/shop/htmlPages', 'delete', {id: id});
    }

    create() {
        return Ajax.post('/shop/htmlPages', 'create');
    }

    saveHtml(id, content) {
        return Ajax.post('/shop/htmlPages', 'saveHtml', {id: id, data:content});
    }

    getHtmlMeta(id){
        return Ajax.post('/shop/htmlPages', 'getHtmlMeta', {id: id});
    }
}

module.exports = new Vendors();