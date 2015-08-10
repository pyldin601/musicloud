var Empty = {},
    Nil = [],
    dateFormat = "dd.MM.yy HH:mm:ss",
    maxFileSize = 512 * 1024 * 1024,
    templatePath = "/public/js/application/templates";

/**
 * Returns function for synchronizing one-typed objects
 * returned by REST API where {key} is unique key.
 *
 * @returns {Function}
 */
function sync(key) {

    var cache = {};

    function func(coll) {
        var id, result = [];
        for (var i = 0; i < coll.length; i += 1) {
            id = coll[i][key];
            if (id in cache) {
                for (var k in coll[i]) if (coll[i].hasOwnProperty(k)) {
                    cache[id][k] = coll[i][k]
                }
                result.push(cache[id]);
            } else {
                cache[id] = coll[i];
                result.push(coll[i]);
            }
        }
        return result;
    }

    func.remove = function (coll) {
        for (var i = 0; i < coll.length; i += 1) {
            if (coll[i].id in cache) {
                delete cache[coll[i].id]
            }
        }
    };

    return func;

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


function iif(expression, onTrue, onFalse) {
    return expression ? onTrue : onFalse
}
