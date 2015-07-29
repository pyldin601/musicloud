/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.factory("AccountService", ["$http", function ($http) {
    return {
        login:  function (data) {
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
        artists: function (offset, filter) {
            return $http.get("/api/catalog/artists?o="+offset+"&q="+encodeURI(filter));
        },
        albums: function (offset, filter) {
            return $http.get("/api/catalog/albums?o="+offset+"&q="+encodeURI(filter));
        },
        genres: function (offset, filter) {
            return $http.get("/api/catalog/genres?o="+offset+"&q="+encodeURI(filter));
        },
        tracks: function (opts, offset) {
            var uri = Empty;

            uri.o = offset;

            if (opts.filter) { uri.q = opts.filter }
            if (opts.artist) { uri.artist = opts.artist }
            if (opts.album)  { uri.album = opts.album }
            if (opts.genre)  { uri.genre = opts.genre }

            return $http.get("/api/catalog/tracks?" + serialize_uri(uri));
        }
    };
}]);

homecloud.factory("LibraryService", ["$http", function ($http) {
    return {
        tracksByArtist: function (artist) {
            return $http.get("/api/catalog/tracks/by-artist/" + encodeURI(artist));
        },
        tracksByAlbum: function (album, artist) {
            return $http.get("/api/catalog/tracks/by-album/" + encodeURI(artist) + "/" + encodeURI(album));
        },

        albumsByArtist: function (artist) {
            return $http.get("/api/catalog/albums/by-artist/" + encodeURI(artist));
        }
    };
}]);

homecloud.factory("Library", [function () {
    return {
        groupAlbums: function (tracks) {
            var albumsList = [],
                index,
                getAlbumIndex = function (album) {
                    for (var j = 0; j < albumsList.length; j += 1) {
                        if (albumsList[j].title == album) {
                            return j;
                        }
                    }
                    return -1;
                };
            for (var i = 0; i < tracks.length; i += 1) {
                index = getAlbumIndex(tracks[i].album);
                if (index == -1) {
                    albumsList.push({
                        title: tracks[i].album,
                        cover_id: tracks[i].cover_file_id,
                        year: tracks[i].date,
                        tracks: [ tracks[i] ]
                    });
                } else {
                    albumsList[index].tracks.push(tracks[i]);
                }
            }
            return albumsList;
        }
    };
}]);

