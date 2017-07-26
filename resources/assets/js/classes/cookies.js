class Cookies {
    get(name) {
        let matches = document.cookie.match(new RegExp(
            '(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    set(name, value, options) {
        options = options || {};

        let expires = options.expires;

        if (typeof expires === 'number' && expires) {
            let d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }


        let updatedCookie = name + '=' + encodeURIComponent(value);

        for (let propName in options) {
            updatedCookie += '; ' + propName;
            let propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += '=' + propValue;
            }
        }

        document.cookie = updatedCookie;
    }
    unset(name){
        this.set(name,'',{expires:-1});
    }
}

module.exports = {
    install(Vue){
        Vue.Cookies = window.Cookies = new Cookies();
    }
};