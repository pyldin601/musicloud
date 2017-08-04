var Empty = {},
    Nil = [],
    dateFormat = "dd.MM.yy HH:mm:ss",
    maxFileSize = 512 * 1024 * 1024,
    templatePath = "/views";


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

function array_copy(src, dst) {
    while (dst.length) {
        dst.pop()
    }
    for (var i = 0; i < src.length; i += 1) {
        dst[i] = src[i];
    }
}

function array_add(src, dst) {
    for (var i = 0; i < src.length; i += 1) {
        dst.push(src[i]);
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


function iif(expression, onTrue, onFalse) {
    return expression ? onTrue : onFalse
}

function decodeUriPlus(uri) {
    return decodeURIComponent(uri.replace(/^_$/g, '').replace(/\+/g, '%20'));
}

function padLeft(expression, fillChars) {
    var expLength   = ("" + expression).length,
        fillLength  = ("" + fillChars).length;
    if (expLength > fillLength)
        return expression;
    return ("" + fillChars + expression).substring(expLength);
}

function htmlToText(html) {
    var tag = document.createElement('div');
    tag.innerHTML = html;
    return tag.innerText;
}

function csvToObject(csvContent) {
    return Papa.parse(csvContent);
}