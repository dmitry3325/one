class Funs{
    cloneObject(obj) {
        let clone = {};
        for(let i in obj) {
            if(typeof(obj[i])==="object" && obj[i] !== null)
                clone[i] = this.cloneObject(obj[i]);
            else
                clone[i] = obj[i];
        }
        return clone;
    }
}

module.exports = new Funs();