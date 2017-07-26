class Ajax {
    constructor() {
        this.requests = [];
        return this;
    }

    post(className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        return new Promise((resolve, reject) => {
            let def = axios.post(className + '?method=' + method, {'params': data});
            def.then(function (response) {
                if (response.data) {
                    resolve(response.data);
                    if (typeof callbackSuccess === 'function') {
                        callbackSuccess(response.data);
                    }
                }
            }, function (response) {
                if (response.data) {
                    reject(response.data);
                    if (typeof callBackError === 'function') {
                        callBackError(response.data)
                    }
                }
            });
        });
    }

    get(className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        let func = '';
        if (method) {
            func = '?method=' + method;
        }

        let params = '';
        if (data) {
            params = [];
            for (var i in data) {
                params.push('params[' + i + ']=' + data[i]);
            }
            params = params.join('&');

            if (params.length) {
                params = ((func) ? '&' : '?') + params;
            }
        }

        return new Promise((resolve, reject) => {
            let def = axios.get(className + func + params);
            def.then(function (response) {
                if (response.data) {
                    resolve(response.data);
                    if (typeof callbackSuccess === 'function') {
                        callbackSuccess(response.data);
                    }
                }
            }, function (response) {
                if (response.data) {
                    reject(response.data);
                    if (typeof callBackError === 'function') {
                        callBackError(response.data)
                    }
                }
            });
        });
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

    getHistory() {
        return this.requests;
    }

    pushToHistory(request) {
        this.requests.push(request);
        return this;
    }
}

export default {
    install(Vue) {
        window.Ajax = window.J = Vue.Ajax = Vue.J = new Ajax();
    }
}