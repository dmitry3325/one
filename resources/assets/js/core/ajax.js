class Ajax {
    constructor() {
        this.requests = [];
        Vue.http.options.emulateJSON = true;
        return this;
    }

    post(className, method, data, callbackSuccess, callBackError) {
        if (typeof className === 'undefined') return false;
        if (typeof method === 'undefined') method = 'get';
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

    getHistory() {
        return this.requests;
    }

    pushToHistory(request) {
        this.requests.push(request);
        return this;
    }
}
export default new Ajax();