var Empty = {},
    Nil = [];

Function.prototype.curry = function () {
    var args1 = Array.prototype.slice.call(arguments),
        that = this;
    return function () {
        var args2 = Array.prototype.slice.call(arguments);
        return that.apply(that, args1.concat(args2));
    };
};

Function.prototype.lcurry = Function.prototype.curry;

Function.prototype.rcurry = function () {
    var args1 = Array.prototype.slice.call(arguments),
        that = this;
    return function () {
        var args2 = Array.prototype.slice.call(arguments);
        return that.apply(that, args2.concat(args1));
    };
};

function sync() {
    var cache = {};
    return function (coll) {
        var id, result = [];
        for (var i = 0; i < coll.length; i++) {
            id = coll[i].id;
            if (id in cache) {
                result.push(cache[id]);
            } else {
                cache[id] = coll[i];
                result.push(coll[i]);
            }
        }
        return result;
    }
}

function serialize_uri(obj) {
    var str = "";
    for (var key in obj) if (obj.hasOwnProperty(key)) {
        if (str != "") {
            str += "&";
        }
        str += key + "=" + encodeURIComponent(obj[key]);
    }
    return str;
}