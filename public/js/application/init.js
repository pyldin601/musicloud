var Empty = {},
    Nil = [],
    dateFormat = "dd.MM.yyyy HH:mm:ss";

/**
 * Returns function for synchronizing one-typed objects
 * returned by REST API where {key} is unique key.
 *
 * @returns {Function}
 */
function sync(key) {
    var cache = {};
    return function (coll) {
        var id, result = [];
        for (var i = 0; i < coll.length; i++) {
            id = coll[i][key];
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

