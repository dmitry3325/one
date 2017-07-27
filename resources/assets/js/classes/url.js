class Url {
    constructor() {

        this.URL = {};
        this.current = {};

        let _goDeep = function (obj, arr, val) {
            let key = arr[0].replace(/\[|\]/g, '');
            arr.splice(0, 1);
            if (key === '') key = 0;
            if (!arr.length) {
                obj[key] = val;
            } else {
                if (!obj[key]) obj[key] = {};
                _goDeep(obj[key], arr, val);
            }

        };

        let getparams = window.location.search;
        if (getparams.length > 0) {
            let pArr = getparams.substring(1).split('&');
            for (let i in pArr) {
                let p = pArr[i].split('=', 2);
                if (p[0] !== undefined && p[1] !== undefined) {
                    let m = p[0].match(/(\[(.*?)\]+)/g);
                    if (m) {
                        let key = p[0].split('[')[0];
                        if (!this.current[key]) {
                            this.current[key] = {};
                        }
                        _goDeep(this.current[key], m, p[1]);
                        this.set(key, this.current[key], false);
                    } else {
                        this.current[p[0]] = p[1];
                        this.set(p[0], this.current[p[0]], false);
                    }
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
    install(Vue) {
        Vue.Url = window.Url = new Url();

    }
};