/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.filter("count", function () {
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

var filters = {
    artist: function (artist) { return artist || "Unknown Artist" },
    album:  function (album)  { return album || "Unknown Album" },
    genre:  function (genre)  { return genre || "Unknown Genre" },
    title:  function (title)  { return title || "Unknown Title" }
};

MusicLoud.filter("albumFilter",  function () { return filters.album });
MusicLoud.filter("artistFilter", function () { return filters.artist });
MusicLoud.filter("titleFilter",  function () { return filters.title });

MusicLoud.filter("getTitle", function () {
    return function (track) {
        if (!track) return;
        return track.track_title || track.file_name || "Unknown Title";
    };
});

MusicLoud.filter("getArtist", function () {
    return function (track) {
        if (!track) return;
        return filters.artist(track.track_artist);
    };
});

MusicLoud.filter("getAlbumArtist", function () {
    return function (track) {
        if (!track) return;
        return filters.artist(track.album_artist);
    };
});

MusicLoud.filter("getTrackNumber", function () {
    return function (track) {
        return (track.disc_number) ? track.disc_number + "." + padLeft(track.track_number, "00") :
            padLeft(track.track_number, "00");
    };
});

MusicLoud.filter("first", function () {
    return function (data) {
        if (!(data && data.length > 0)) return;
        return data[0];
    };
});

MusicLoud.filter("keys", function () {
    return function (data) {
        if (!data) return;
        return Object.keys(data);
    };
});

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


MusicLoud.filter("bitrateFilter", [function () {
    return function (value) {
        return "" + (parseInt(value / 1000 / 8) * 8) + " kbps";
    };
}]);

MusicLoud.filter("groupGenres", [function () {
    return groupGenres;
}]);

MusicLoud.filter("isVariousArtists", [function () {
    return function (coll) {
        return coll.any(function (el) { return el.album_artist != el.track_artist })
    };
}]);
