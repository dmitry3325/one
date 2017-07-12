class Ajax {
    constructor() {
        this.requests = [];
        return this;
    }

    post(className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof data === 'undefined') data = {};

        let def = axios.post(className + '?method=' + method, {'params': data});
        def.then(function (response) {
            if (typeof callbackSuccess === 'function') {
                if (response.data) {
                    callbackSuccess(response.data);
                }
            }
        }, function (response) {
            if (typeof callBackError === 'function') {
                if (response.data) {
                    callBackError(response.data)
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

        let def = axios.get(className + func + params);
        def.then(function (response) {
            if (typeof callbackSuccess === 'function') {
                if (response.data) {
                    callbackSuccess(response.data);
                }
            }
        }, function (response) {
            if (typeof callBackError === 'function') {
                if (response.data) {
                    callBackError(response.data)
                }
            }
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
export default new Ajax();