let filter = Funs.cloneObject(require('./base.js'));
filter['filters']['content'] = require('./filters_list.js');
module.exports = filter;