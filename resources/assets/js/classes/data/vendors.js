class Vendors {
    constructor() {
        this.allFields = null;
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

    update(id, data) {
        return Ajax.post('/shop/vendors', 'update', {id: id, data:data});
    }
}

module.exports = new Vendors();