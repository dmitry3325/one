export default class Errors{
    constructor() {
        this.errors = {};
    }

    get(field){
        if(this.errors[field]){
            if (typeof this.errors[field] == "string") {
                return this.errors[field];
            } else if(this.errors[field][0]){
                return this.errors[field][0];
            }
        }
        return this;
    }

    has(field){
        return this.errors.hasOwnProperty(field);
    }

    set(errors){
        this.errors = errors;
        return this;
    }

    clear(field){
        delete this.errors[field];
        return this;
    }

    clearAll(){
        this.errors = {};
        return this;
    }
}
