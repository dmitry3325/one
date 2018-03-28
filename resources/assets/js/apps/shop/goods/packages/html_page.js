let base = Funs.cloneObject(require('./base.js'));

let htmlPages = {
    'page': {
        'title':   'Код страницы',
        'content': [
            [
                {
                    'field': 'body',
                    'size': 12
                }
            ]
        ]
    }
};

for (let i in base) {
    htmlPages[i] = base[i];
}

module.exports = htmlPages;