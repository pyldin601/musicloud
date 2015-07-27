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
            return $http.post("/api/catalog/artists", data);
        },
        albums: function (data) {
            return $http.post("/api/catalog/albums", data);
        },
        genres: function (data) {
            return $http.post("/api/catalog/genres", data);
        },
        tracks: function (data) {
            return $http.post("/api/catalog/tracks", data);
        }
    };
}]);

homecloud.factory("LibraryService", ["$http", function ($http) {
    return {
        tracksByArtist: function (artist) {
            return $http.post("/api/catalog/tracks/by-artist/" + encodeURI(artist));
        },
        tracksByAlbum: function (album, artist) {
            return $http.post("/api/catalog/tracks/by-album/" + encodeURI(artist) + "/" + encodeURI(album));
        },

        albumsByArtist: function (artist) {
            return $http.post("/api/catalog/albums/by-artist/" + encodeURI(artist));
        }
    };
}]);