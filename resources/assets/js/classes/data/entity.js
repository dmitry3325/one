class Entity {
    constructor() {
        this.lists = {};
        this.entities = {};
        this.allFields = {};
        this.baseFields = {};
        this.filterMethods = null;
        this.entity = null;
        this.className = '/shop';
    }

    getFilterMethods(force) {
        let self = this;
        if (self.filterMethods && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.filterMethods);
            });
        } else {
            return Ajax.post(this.className, 'getFilterMethods', {}, function (data) {
                self.filterMethods = data;
            });
        }
    }

    getAllFields(entity, force) {
        if (typeof entity === 'undefined' && this.entity) {
            entity = this.entity;
        }

        let self = this;
        if (self.allFields[entity] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.allFields[entity]);
            });
        } else {
            return Ajax.post(this.className, 'getAllFields', {entity: entity}, function (data) {
                self.allFields[entity] = data;
            });
        }
    }

    getBaseFields(entity, force) {
        if (typeof entity === 'undefined' && this.entity) {
            entity = this.entity;
        }

        let self = this;
        if (self.baseFields[entity] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.baseFields[entity]);
            });
        } else {
            return Ajax.post(this.className, 'getBaseFields', {entity: entity}, function (data) {
                self.baseFields[entity] = data;
            });
        }
    }

    getItemsList(entity, options, force) {
        if (typeof options !== 'object' && typeof entity === 'object') {
            force = options;
            entity = options;
            entity = undefined;
        }
        if (typeof entity === 'undefined' && this.entity) {
            entity = this.entity;
        }
        if (typeof options === 'undefined') options = {};

        let self = this;
        let key = JSON.stringify(options);

        if (self.lists[entity] && self.lists[entity].key === key && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.lists[entity].data);
            });
        } else {
            return Ajax.post(this.className, 'getItemsList', {
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

    create(entity, data, getEntity) {
        if (typeof data !== 'object' && typeof entity === 'object' && this.entity) {
            getEntity = data;
            data = entity;
            entity = this.entity;
        }

        return Ajax.post(this.className, 'createEntity', {
            entity: entity,
            data: data,
            'getEntity': getEntity
        });
    }

    update(entity, id, data){
        if (typeof id === 'object' && typeof entity === 'number' && this.entity) {
            data = id;
            id = entity;
            entity = this.entity;
        }

        return Ajax.post(this.className, 'updateEntity', {
            entity: entity,
            id: id,
            data: data
        });
    }

    deleteEntity(entity, id){
        if (typeof entity === 'number' && this.entity) {
            id = entity;
            entity = this.entity;
        }

        return Ajax.post(this.className, 'deleteEntity', {
            entity: entity,
            id: id
        });
    }

    get(entity, id, force) {
        if (typeof entity === 'number' && this.entity) {
            force = id;
            id = entity;
            entity = this.entity;
        }

        let self = this;

        if (this.entities[entity] && this.entities[entity][id] && !force) {
            return new Promise((resolve, reject) => {
                resolve(self.entities[entity][id]);
            });
        } else {
            return Ajax.post(this.className, 'getEntity', {
                entity: entity,
                id: id
            }, function (res) {
                if (!self.entities[entity]) self.entities[entity] = {};
                self.entities[entity][id] = res;
            });
        }
    }
}

class Sections extends Entity {
    constructor() {
        super();
        this.entity = 'Sections';
    }
}

class Filters extends Entity {
    constructor() {
        super();
        this.entity = 'Filters';
    }
}

class Goods extends Entity {
    constructor() {
        super();
        this.entity = 'Goods';
    }
}

class HtmlPages extends Entity {
    constructor() {
        super();
        this.entity = 'HtmlPages';
    }
}

module.exports = {
    'entity': new Entity(),
    'sections': new Sections(),
    'filters': new Filters(),
    'goods': new Goods(),
    'pages': new HtmlPages(),
};