/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.filter("uri", function () {
    return function (path) {
        return encodeURIComponent(path)
    };
});

MusicLoud.filter("dateFilter", ["$filter", function ($filter) {
    return function (value) {
        if (!value) {
            return "-"
        }
        return $filter("date")(value * 1000, dateFormat);
    }
}]);

MusicLoud.filter("groupGenres", [function () {
    return groupGenres;
}]);

MusicLoud.filter("isVariousArtists", [function () {
    return function (coll) {
        return coll.any(function (el) { return el.album_artist != el.track_artist })
    };
}]);
