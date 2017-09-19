class Events {
    constructor() {
        this.events = {};
    }

    on(name, callback) {
        if (typeof this.events[name] === 'undefined') this.events[name] = [];
        this.events[name].push(callback);
    }

    emit(name) {
        if (this.events[name]) {
            for (let i = 0; i < this.events[name].length; i++) {
                if (typeof this.events[name][i] === 'function') {
                    this.events[name][i]();
                }
            }
        }
    }
}

export default {
    install(Vue) {
        Vue.Events = new Events();
    }
}