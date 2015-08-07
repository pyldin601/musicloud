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

/*
    Arithmetical functions
 */
function sum (a, b) { return a + b }
function sub (a, b) { return a - b }
function or  (a, b) { return a || b }
function and (a, b) { return a && b }
