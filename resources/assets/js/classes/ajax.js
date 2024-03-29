class Ajax {
    constructor() {
        this.requests = [];
        this.J = axios.create({
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
            });

        return this;
    }

    post(className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        return new Promise((resolve, reject) => {
            let def = this.J.post(className + '?method=' + method, {'params': data});
            def.then(function (response) {
                if (response.data) {
                    resolve(response.data);
                    if (typeof callbackSuccess === 'function') {
                        callbackSuccess(response.data);
                    }
                }
            }, function (response) {
                reject(response);
                if (typeof callBackError === 'function') {
                    callBackError(response)
                }
            });
        });
    }

    get (className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        let func = '';
        if (method) {
            func = '?method=' + method;
        }

        let params = '';
        if (data) {
            params = this._prepareParams({'params': data}, func);
        }

        return new Promise((resolve, reject) => {
            let def = this.J.get(className + func + params);
            def.then(function (response) {
                if (response.data) {
                    resolve(response.data);
                    if (typeof callbackSuccess === 'function') {
                        callbackSuccess(response.data);
                    }
                }
            }, function (response) {
                reject(response);
                if (typeof callBackError === 'function') {
                    callBackError(response)
                }
            });
        });
    }


    getFile(className, method, data) {
        let func = '';
        if (method) {
            func = '?method=' + method;
        }

        let params = '';
        if (data) {
            params = this._prepareParams({'params': data}, func);
        }

        window.open(className + func + params, '_blank');
    }

    when(defs, callbackSuccess, callBackError) {
        let def = Promise.all(defs);

        let _call = function (response, callback) {
            if (typeof callback === 'function') {
                callback.apply(def, response);
            }
        };

        def.then(function (response) {
            _call(response, callbackSuccess);
        }, function (response) {
            _call(response, callBackError);
        });

        return def;
    }

    _getHistory() {
        return this.requests;
    }

    _pushToHistory(request) {
        this.requests.push(request);
        return this;
    }

    _prepareParams(data, funs) {
        let p = $.param(data);
        return (funs) ? '&' + p : '?' + p;
    }
}

export default {
    install(Vue) {
        window.Ajax = window.J = Vue.Ajax = Vue.J = new Ajax();
    }
}