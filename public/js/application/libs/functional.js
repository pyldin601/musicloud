/**
 * Created by roman on 07.08.15.
 */

Array.prototype.any = function (predicate) {
    for (var i = 0, length = this.length; i < length; i += 1) {
        if (predicate(this[i])) {
            return true;
        }
    }
    return false;
};

Array.prototype.all = function (predicate) {
    for (var i = 0, length = this.length; i < length; i += 1) {
        if (!predicate(this[i])) {
            return false;
        }
    }
    return true;
};

Array.prototype.first = function () {
    if (this.length == 0) {
        return undefined;
    }
    return this[0];
};

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

/*
    Arithmetical functions
 */
function sum (a, b) { return a + b }
function sub (a, b) { return a - b }
function or  (a, b) { return a || b }
function and (a, b) { return a && b }
