class Vendors {
    constructor() {
        this.allFields = null;
        this.baseFields = null;
        this.items = {};
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

    getBaseFields(force) {
        let self = this;
        if (self.baseFields && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.baseFields);
            });
        } else {
            return Ajax.post('/shop/vendors', 'getBaseFields', {}, function (data) {
                self.baseFields = data;
            });
        }
    }

    update(id, data, force) {
        let self = this;
        if (self.items[id] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.items[id]);
            });
        } else {
            return Ajax.post('/shop/vendors', 'update', {id: id, data:data}, function (data) {
                self.items[id] = data;
            });
        }
    }
}

module.exports = new Vendors();