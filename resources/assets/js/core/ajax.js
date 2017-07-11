class Ajax {
    constructor() {
        this.requests = [];
        Vue.http.options.emulateJSON = true;
        return this;
    }

    post(className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        let def = Vue.http.post(className + '?method=' + method, {'params': data});

        def.then(function (response) {
            if (typeof callbackSuccess === 'function') {
                if (response.body) {
                    callbackSuccess(response.body);
                }
            }
        }, function (response) {
            if (typeof callBackError === 'function') {
                if (response.body) {
                    callBackError(response.body)
                }
            }
        });

        return def;
    }

    get(className, method, data, callbackSuccess, callBackError){
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        let func = '';
        if(method){
            func = '?method=' + method;
        }

        let params = '';
        if(data) {
            params = [];
            for (var i in data) {
                params.push('params[' + i + ']=' + data[i]);
            }
            params = params.join('&');

            if (params.length) {
                params = ((func) ? '&' : '?') + params;
            }
        }

        let res = Vue.http.get(className + func + params);
        if(res.ok){
            if (typeof callbackSuccess === 'function') {
                if (response.body) {
                    callbackSuccess(response.body);
                }
            }
        } else {
            if (typeof callBackError === 'function') {
                if (response.body) {
                    callBackError(response.body)
                }
            }
        }
        return res;
    }

    getHistory() {
        return this.requests;
    }

    pushToHistory(request) {
        this.requests.push(request);
        return this;
    }
}
export default new Ajax();