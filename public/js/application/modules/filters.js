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

homecloud.filter("groupBy", function () {
    var results={};
    return function (data, key) {
        if (!(data && key)) return;
        var result;
        if(!this.$id){
            result={};
        }else{
            var scopeId = this.$id;
            if(!results[scopeId]){
                results[scopeId]={};
                this.$on("$destroy", function() {
                    delete results[scopeId];
                });
            }
            result = results[scopeId];
        }

        for(var groupKey in result) if (result.hasOwnProperty(groupKey))
            result[groupKey].splice(0,result[groupKey].length);

        for (var i=0; i<data.length; i++) {
            if (!result[data[i][key]])
                result[data[i][key]]=[];
            result[data[i][key]].push(data[i]);
        }

        var keys = Object.keys(result);
        for(var k=0; k<keys.length; k++){
            if(result[keys[k]].length===0)
                delete result[keys[k]];
        }
        return result;
    };
});

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