/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.filter("count", function () {
    return function (value) {
        if (value === undefined) {
            return undefined;
        }
        var suffix = ["", "k", "m", "g"],
            index = 0;

        while (value > 1000) {
            value /= 1000;
            index += 1;
        }
        return index == 0 ? value : value.toFixed(1) + suffix[index];
    };
});

homecloud.filter("mmss", function () {
    return function (value) {
        var sec_num = parseInt(value, 10);
        var minutes = Math.floor(sec_num / 60);
        var seconds = sec_num - (minutes * 60);

        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}

        return minutes+':'+seconds;
    }
});

homecloud.filter("albumFilter", function () {
    return function (album) {
        return album ? album : "Unknown Album";
    };
});

homecloud.filter("getTitle", function () {
    return function (track) {
        if (!track) return;
        return track.title || track.file_name || "Unknown Title";
    };
});

homecloud.filter("getArtist", function () {
    return function (track) {
        if (!track) return;
        return track.artist || "Unknown Artist";
    };
});

homecloud.filter("getAlbumArtist", function () {
    return function (track) {
        if (!track) return;
        return track.album_artist || "Unknown Artist";
    };
});

homecloud.filter("groupBy", ["$timeout", function ($timeout) {
    return function (data, key) {
        if (!key) return data;
        var outputPropertyName = '__groupBy__' + key;
        if(!data[outputPropertyName]){
            var result = {};
            for (var i=0;i<data.length;i++) {
                if (!result[data[i][key]])
                    result[data[i][key]]=[];
                result[data[i][key]].push(data[i]);
            }
            Object.defineProperty(data, outputPropertyName, {enumerable:false, configurable:true, writable: false, value:result});
            $timeout(function(){delete data[outputPropertyName];},0,false);
        }
        return data[outputPropertyName];
    };
}]);

homecloud.filter("first", function () {
    return function (data) {
        if (!(data && data.length > 0)) return;
        return data[0];
    };
});

homecloud.filter("keys", function () {
    return function (data) {
        if (!data) return;
        return Object.keys(data);
    };
});