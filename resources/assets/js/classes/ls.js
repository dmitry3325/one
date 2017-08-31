class LS {
    constructor() {
        this.LS = {};

        this.page = location.origin;

        if (typeof localStorage[this.page] !== "undefined") {
            let saved = localStorage[this.page];
            if (saved !== '') {
                try {
                    this.LS = JSON.parse(saved);
                } catch (ex) {
                    console.log(ex);
                    localStorage[this.page] = '';
                    this.LS = {};
                }
            }
        }
    }

    set(key, value) {
        this.LS[key] = value;
        localStorage[this.page] = JSON.stringify(this.LS);
    }

    get(key) {
        if(key) return this.LS[key];
        else return this.LS;
    }

    unset(key) {
        if (this.LS[key] !== undefined) {
            delete this.LS[key];
            localStorage[this.page] = JSON.stringify(this.LS);
        }
    }
}

module.exports = {
    install(Vue){
        Vue.Ls = window.Ls = new LS();
    }
};