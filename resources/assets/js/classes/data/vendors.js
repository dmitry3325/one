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
            return Ajax.post('/shop/vendors', 'getAllFields', {}, function (data) {
                self.allFields = data;
            });
        }
    }

    getVendorsList(options, force) {
        let self = this;
        if (self.all && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.all);
            });
        } else {
            return Ajax.post('/shop/vendors', 'getVendorsList', {options}, function (data) {
                if (data.result && data.list) {
                    self.all = data.list;
                }
            });
        }
    }

    update(id, data) {
        return Ajax.post('/shop/vendors', 'update', {id: id, data:data});
    }
}

module.exports = new Vendors();