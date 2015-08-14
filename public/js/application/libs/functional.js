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
    return this[0];
};

Array.prototype.tail = function () {
    return this.slice(1);
};

Array.prototype.last = function () {
    return this[this.length - 1];
};

Array.prototype.max = function() {
    return Math.max.apply(null, this);
};

Array.prototype.min = function() {
    return Math.min.apply(null, this);
};

Array.prototype.isVarious = function () {
    var first;
    if (this.length == 0) {
        return false;
    }
    first = this.first();
    return !this.any(function (e) { return e !== first });
};

Array.prototype.distinct = function () {
    var acc = {};
    for (var i = 0; i < this.length; i += 1) {
        if (acc[this[i]] === undefined) {
            acc[this[i]] = true;
        }
    }
    return Object.keys(acc);
};

Array.prototype.chunk = function(chunkSize) {
    var array=this;
    return [].concat.apply([],
        array.map(function(elem,i) {
            return i%chunkSize ? [] : [array.slice(i,i+chunkSize)];
        })
    );
};

Array.prototype.avg = function () {
    var initial = 0;
    return (this.length > 0) ? this.reduce(sum, initial) / this.length : initial;
};

/*
    Currying functions
 */
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
function sum  (a, b) { return a + b }
function sub  (a, b) { return a - b }
function or   (a, b) { return a || b }
function and  (a, b) { return a && b }
function prod (a, b) { return a * b }
function div  (a, b) { return a / b }

/*
    Help functions for Map
 */
function field(name) {
    return function (obj) { return obj[name] }
}


/*
    Tests
*/
function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
