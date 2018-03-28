class Entity {
    constructor() {
        this.lists = {};
        this.entities = {};
        this.allFields = {};
        this.baseFields = {};
        this.baseFields = {};
        this.filterMethods = null;
        this.entity = null;
        this.className = '/shop';
    }

    getFilterMethods(force) {
        if (!this.filterMethods || force) {
            this.filterMethods = Ajax.post(this.className, 'getFilterMethods');
        }
        return this.filterMethods;
    }

    getAllFields(entity, force) {
        if (typeof entity === 'undefined' && this.entity) {
            entity = this.entity;
        }
        if (!this.allFields[entity] || force) {
            this.allFields[entity] = Ajax.post(this.className, 'getAllFields', {entity: entity});
        }
        return this.allFields[entity];
    }


    getBaseFields(entity, force) {
        if (typeof entity === 'undefined' && this.entity) {
            entity = this.entity;
        }

        if (!this.baseFields[entity] || force) {
            this.baseFields[entity] = Ajax.post(this.className, 'getBaseFields', {entity: entity});
        }
        return this.baseFields[entity];
    }

    getItemsList(entity, options, force) {
        if (typeof options !== 'object' && typeof entity === 'object') {
            force = options;
            entity = options;
        }
        if (typeof entity === 'undefined' && this.entity) {
            entity = this.entity;
        }
        if (typeof options === 'undefined') options = {};

        let key = JSON.stringify(options);

        if (!this.lists[entity] || this.lists[entity].key === key || force) {
            let def = Ajax.post(this.className, 'getItemsList', {
                entity:  entity,
                options: options
            });
            this.lists[entity] = {
                'key': key,
                'def': def
            };
        }
        return this.lists[entity].def;
    }

    create(entity, data, getEntity) {
        if (typeof data !== 'object' && typeof entity === 'object' && this.entity) {
            getEntity = data;
            data = entity;
            entity = this.entity;
        }

        return Ajax.post(this.className, 'createEntity', {
            entity:      entity,
            data:        data,
            'getEntity': getEntity
        });
    }

    update(entity, id, data) {
        if (typeof id === 'object' && typeof entity === 'number' && this.entity) {
            data = id;
            id = entity;
            entity = this.entity;
        }

        return Ajax.post(this.className, 'updateEntity', {
            entity: entity,
            id:     id,
            data:   data
        });
    }

    deleteEntity(entity, id) {
        if (typeof entity === 'number' && this.entity) {
            id = entity;
            entity = this.entity;
        }

        return Ajax.post(this.className, 'deleteEntity', {
            entity: entity,
            id:     id
        });
    }

    get(entity, id, force) {
        if (typeof entity === 'number' && this.entity) {
            force = id;
            id = entity;
            entity = this.entity;
        }
        if (!this.entities[entity] || !this.entities[entity][id] || force) {
            if (!this.entities[entity]) this.entities[entity] = {};

            this.entities[entity][id] = Ajax.post(this.className, 'getEntity', {
                entity: entity,
                id:     id
            });
        }
        return  this.entities[entity][id];
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
    'entity':   new Entity(),
    'sections': new Sections(),
    'filters':  new Filters(),
    'goods':    new Goods(),
    'pages':    new HtmlPages(),
};