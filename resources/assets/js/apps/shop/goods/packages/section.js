let Funs = require('../../../../classes/funs.js');
let section = Funs.cloneObject(require('./base.js'));

let filters = [];
for(let i = 1; i<=8; i++){
    filters.push([
        {
            'field': 'filter_'+i,
            'size': 9
        },
        {
            'field': 'filter_'+i+'_use_in_autocreate',
            'size': 3
        }
    ]);
}
section['filters']['content'] = filters;

module.exports = section;