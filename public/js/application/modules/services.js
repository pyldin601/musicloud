/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.factory("AccountService", ["$http", function ($http) {
    return {
        login: function (data) {
            return $http.post("/api/login", data);
        },
        logout: function () {
            return $http.post("/api/logout");
        },
        init: function () {
            return $http.get("/api/self");
        }
    };
}]);

homecloud.factory("TrackService", ["$http", function ($http) {
    return {
        create: function () {
            return $http.post("/api/track/create");
        },
        upload: function (data) {
            return $http.post("/api/track/upload", data);
        },
        unlink: function (data) {
            return $http.post("/api/track/delete", data);
        },
        edit: function (data) {
            // todo: Implement this method
            throw new Error("Method not implemented");
        }
    };
}]);

homecloud.factory("SearchService", ["$http", function ($http) {
    return {
        artists: function (opts, offset) {
            var uri = {};

            uri.o = offset;

            if (opts.filter) {
                uri.filter = opts.filter
            }

            return $http.get("/api/catalog/artists?" + serialize_uri(uri));
        },
        albums: function (opts, offset) {
            var uri = {};

            uri.o = offset;

            if (opts.filter) {
                uri.filter = opts.filter
            }

            return $http.get("/api/catalog/albums?" + serialize_uri(uri));
        },
        genres: function (opts, offset) {
            var uri = {};

            uri.o = offset;

            if (opts.filter) {
                uri.q = opts.filter
            }

            return $http.get("/api/catalog/genres?" + serialize_uri(uri));
        },
        tracks: function (opts, offset) {

            var uri = {};

            uri.o = offset;

            if (opts.filter !== undefined) {
                uri.q = opts.filter
            }
            if (opts.artist !== undefined) {
                uri.artist = opts.artist
            }
            if (opts.album !== undefined) {
                uri.album = opts.album
            }
            if (opts.genre !== undefined) {
                uri.genre = opts.genre
            }

            return $http.get("/api/catalog/tracks?" + serialize_uri(uri));
        }
    };
}]);

homecloud.factory("Library", [function () {
    var obj = {
        groupAlbums: function (tracks) {
            var albumsList = [];
            obj.addToGroup(albumsList, tracks);
            return albumsList;
        },
        addToGroup: function (coll, tracks) {
            var index,
                key,
                getAlbumIndex = function (album) {
                    for (var j = 0; j < coll.length; j += 1) {
                        if (coll[j].key == album) {
                            return j;
                        }
                    }
                    return -1;
                },
                getAlbumIndexCached = getAlbumIndex.memoize();
            for (var i = 0; i < tracks.length; i += 1) {
                key = tracks[i].album_artist + "/" + tracks[i].track_album;
                index = getAlbumIndexCached.call(key);
                if (index == -1) {
                    coll.push({
                        key: key,
                        title: tracks[i].track_album,
                        album_artist: tracks[i].album_artist,
                        small_cover_id: tracks[i].small_cover_id,
                        middle_cover_id: tracks[i].middle_cover_id,
                        big_cover_id: tracks[i].big_cover_id,
                        year: tracks[i].track_year,
                        various_artists: (tracks[i].album_artist !== tracks[i].track_artist),
                        tracks: [
                            tracks[i]
                        ]
                    });
                    getAlbumIndexCached.reset();
                } else {
                    coll[index].tracks.push(tracks[i]);
                    if (tracks[i].album_artist !== tracks[i].track_artist) {
                        coll[index].various_artists = true;
                    }
                }
            }
        }
    };
    return obj;
}]);

homecloud.factory("StatsService", ["$http", function ($http) {
    return {
        incrementPlays: function (track_id) {
            return $http.post("/api/stats/played", {id: track_id});
        },
        incrementSkips: function (track_id) {
            return $http.post("/api/stats/skipped", {id: track_id});
        }
    }
}]);

homecloud.factory("SyncService", [function () {
    var trackSync = sync();
    var artistSync = sync();
    var albumSync = sync();
    return {
        tracks: function (coll) {
            return trackSync(coll);
        },
        track: function (track) {
            return trackSync([track]).shift();
        },

        artists: function (coll) {
            return artistSync(coll);
        },
        artist: function (artist) {
            return artistSync([artist]).shift();
        },

        albums: function (coll) {
            return albumSync(coll);
        },
        album: function (album) {
            return albumSync([album]).shift();
        }
    }
}]);