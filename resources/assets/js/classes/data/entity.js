class Entity {
    constructor() {
        this.allFields = {};
        this.baseFields = {};
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
        if (self.allFields[entity] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.allFields[entity]);
            });
        } else {
            return Ajax.post('/shop/lists', 'getBaseFields', {entity: entity}, function (data) {
                self.allFields[entity] = data;
            });
        }
    }
}

module.exports = new Entity();