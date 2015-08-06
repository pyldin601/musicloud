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

Function.prototype.memoize = function () {
    var cache = {}, that = this;
    return {
        call: function () {
            var args = Array.prototype.slice.call(arguments),
                key = JSON.stringify(args);
            if (key in cache) {
                return cache[key];
            } else {
                return cache[key] = that.apply(that, args);
            }
        },
        reset: function () {
            cache = {};
        }
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

function sum(a, b) {
    return a + b
}

function or(a, b) {
    return a || b
}