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
        artists: function (data) {
            return $http.get("/api/catalog/artists", data);
        },
        albums: function (data) {
            return $http.get("/api/catalog/albums", data);
        },
        genres: function (data) {
            return $http.get("/api/catalog/genres", data);
        },
        tracks: function (data) {
            return $http.get("/api/catalog/tracks", data);
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
                        cover_id: tracks[i].id,
                        year: tracks[i].date,
                        has_cover: tracks[i].cover_file_id !== null,
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