/**
 * Created by Roman on 27.07.2015.
 */

var mediacloud = angular.module("HomeCloud");

mediacloud.config(["$routeProvider", function ($routeProvider) {
    var templatePath = "/public/js/application/templates";

    $routeProvider.when("/artists/", {
        templateUrl: templatePath + "/artists-view.html",
        controller: "AllArtistsViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                return SearchService.artists(Empty, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "artists"
        }
    });

    $routeProvider.when("/albums/", {
        templateUrl: templatePath + "/albums-view.html",
        controller: "AllAlbumsViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                return SearchService.albums(Empty, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "albums"
        }
    });

    $routeProvider.when("/genres/", {
        templateUrl: templatePath + "/genres-view.html",
        controller: "AllGenresViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                return SearchService.genres(Empty, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "genres"
        }
    });

    $routeProvider.when("/artist/:artist", {
        templateUrl: templatePath + "/grouped-view.html",
        controller: "ArtistViewController",
        resolve: {
            Resolved: ["SearchService", "$route", "$location", function (SearchService, $route, $location) {
                return SearchService.tracks({ artist_id: $route.current.params.artist }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "artists"
        }
    });

    $routeProvider.when("/album/:album", {
        templateUrl: templatePath + "/grouped-view.html",
        controller: "AlbumViewController",
        resolve: {
            Resolved: ["SearchService", "$route", "$location", function (SearchService, $route, $location) {
                return SearchService.tracks({ album_id: $route.current.params.album }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "albums"
        }
    });

    $routeProvider.when("/genre/:genre", {
        templateUrl: templatePath + "/view-group-genre.html",
        controller: "GenreViewController",
        resolve: {
            Resolved: ["SearchService", "$route", "$location", function (SearchService, $route, $location) {
                return SearchService.tracks({ genre_id: $route.current.params.genre }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "genres"
        }
    });

    $routeProvider.when("/tracks/grouped", {
        templateUrl: templatePath + "/grouped-view.html",
        controller: "GroupViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                return SearchService.tracks($location.search(), 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        special: {
            section: "tracks"
        }
    });

    $routeProvider.otherwise({
        redirectTo: "/artists/"
    });
}]);