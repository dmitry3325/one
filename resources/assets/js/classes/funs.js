class Funs {
    cloneObject(obj) {
        let clone = {};
        for (let i in obj) {
            if (typeof(obj[i]) === "object" && obj[i] !== null)
                clone[i] = this.cloneObject(obj[i]);
            else
                clone[i] = obj[i];
        }
        return clone;
    }

    sortSubObj(obj, sub, dir) {
        let sorted = [];

        let sortBy = {};
        if (typeof sub != 'object') {
            sortBy[sub] = dir;
        } else {
            sortBy = sub;
        }

        for (let i in obj) {
            obj[i]._array_key = i;
            sorted.push(obj[i]);
        }
        let res = sorted.sort(function (a, b) {
            let r = 0;
            for (let i in sortBy) {
                let dir = sortBy[i];
                dir = !dir ? "asc" : dir;
                dir = (dir.toLowerCase() == "desc") ? -1 : 1;

                if (a[i] == b[i]) continue;
                r = a[i] > b[i] ? dir : -dir;
                break;
            }
            return r;
        });
        for (let i = 0; i < res.length; i++) {
            delete res[i]._array_key;
        }
        return res;
    }

}

module.exports = new Funs();