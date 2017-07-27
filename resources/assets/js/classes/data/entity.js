class Entity {
    constructor() {
        this.lists = {};
        this.allFields = {};
        this.baseFields = {};
        this.filterMethods = null;
    }

    getFilterMethods(force) {
        let self = this;
        if (self.filterMethods && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.filterMethods);
            });
        } else {
            return Ajax.post('/shop/lists', 'getFilterMethods', {}, function (data) {
                self.filterMethods = data;
            });
        }
    }

    getAllFields(entity, force) {
        let self = this;
        if (self.allFields[entity] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.allFields[entity]);
            });
        } else {
            return Ajax.post('/shop/lists', 'getAllFields', {entity: entity}, function (data) {
                self.allFields[entity] = data;
            });
        }
    }

    getBaseFields(entity, force) {
        let self = this;
        if (self.baseFields[entity] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.baseFields[entity]);
            });
        } else {
            return Ajax.post('/shop/lists', 'getBaseFields', {entity: entity}, function (data) {
                self.baseFields[entity] = data;
            });
        }
    }

    getItemsList(entity, options, force) {
        if (typeof options === 'undefined') options = {};
        let self = this;
        let key = JSON.stringify(options);

        if (self.lists[entity] && self.lists[entity].key === key && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.lists[entity].data);
            });
        } else {
            return Ajax.post('/shop/lists', 'getItemsList', {
                entity: entity,
                options: options
            }, function (res) {
                self.lists[entity] = {
                    'key': key,
                    'data': res
                };
            });
        }
    }
}

module.exports = new Entity();