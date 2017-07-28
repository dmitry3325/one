class Vendors {
    constructor() {
        this.allFields = null;
        this.baseFields = null;
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
}

module.exports = new Vendors();