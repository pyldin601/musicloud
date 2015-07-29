var Empty = {};

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