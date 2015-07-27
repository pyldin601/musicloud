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

homecloud.filter("time", function () {
    return function (value) {
        var sec_num = parseInt(value, 10);
        var minutes = Math.floor(sec_num / 60);
        var seconds = sec_num - (minutes * 60);

        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}

        return minutes+':'+seconds;
    }
});