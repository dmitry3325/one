class Url {
    constructor() {

        this.URL = {};
        this.current = {};

        let getparams = window.location.search;
        if (getparams.length > 0) {
            let pArr = getparams.substring(1).split('&');
            for (let i in pArr) {
                let p = pArr[i].split('=', 2);
                if (p[0] !== undefined && p[1] !== undefined) {
                    let m = p[0].match(/([^\[]+)(?:\[([^\]]+)\])?/);
                    let key = '';
                    if (m[2] === undefined) {
                        key = p[0];
                        this.current[key] = p[1];
                    } else {
                        key = m[1];
                        if (this.current[key] === undefined) this.current[key] = {};
                        this.current[key][m[2]] = p[1];
                    }
                    this.set(key, this.current[key], false);
                }
            }
        }
    }

    get(key) {
        return this.URL[key];
    }

    set(key, value, push) {
        if (typeof value === 'object') {
            if (typeof this.URL[key] !== 'object') this.URL[key] = {};
            for (let k in value) {
                this.URL[key][k] = value[k];
            }
        } else {
            this.URL[key] = value;
        }

        let u = this.useStringify ? this.stringify(self.URL) : decodeURIComponent($.param(this.URL));
        if (push === undefined && push !== false) history.pushState(this.URL, '', '?' + u);
    }

    unset(key) {
        if (this.URL[key] !== undefined) {
            delete this.URL[key];
            let get = [];
            let j = 0;
            for (let i in this.URL) {
                get[j++] = ( i + '=' + this.URL[i] );
            }
            history.pushState(this.URL, '', ( get.length > 0 ? '?' + get.join('&') : '?'));
        }
    }

    stringify(u, k) {
        let s = [];
        for (let i in u) {
            if (typeof u[i] === 'object') {
                let v = this.stringify(u[i], k ? k + '[' + i + ']' : i);
                if (!v) continue;
                s.push(v);
            } else {
                s.push(encodeURIComponent(k ? k + '[' + i + ']' : i) + '=' + u[i]);
            }
        }
        return s.join('&');
    }
}

module.exports = {
    install(Vue){
        Vue.Url = window.Url = new Url();

    }
};