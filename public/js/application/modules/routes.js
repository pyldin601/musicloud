/**
 * Created by Roman on 27.07.2015.
 */


var mediacloud = angular.module("HomeCloud");

mediacloud.config(["$routeProvider", "$locationProvider", function ($routeProvider, $locationProvider) {

    $locationProvider.html5Mode(true);
    $locationProvider.baseHref = "/library/";

    $routeProvider.when("/artists/", {
        templateUrl: templatePath + "/artists-view.html",
        controller: "AllArtistsViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                var q = $location.search().q;
                return SearchService.artists({ q: q }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        title: "Artists",
        special: {
            section: "artists"
        }
    });

    $routeProvider.when("/artist/:artist", {
        templateUrl: templatePath + "/single-artist-view.html",
        controller: "ArtistViewController",
        resolve: {
            Header: ["$route", "HeadersService", function ($route, HeadersService) {
                var artist = decodeUriPlus($route.current.params.artist);
                return HeadersService.artist(artist).then(function (response) {
                    return response.data;
                });
            }],
            Resolved: ["SearchService", "$location", "$route", "$filter",
                function (SearchService, $location, $route, $filter) {
                    var artist = decodeUriPlus($route.current.params.artist);
                    $route.current.title = $filter("artistFilter")(artist);
                    return SearchService.tracks({ artist: artist, limit: -1, compilations: 0 }, 0).then(function (response) {
                        return response;
                    }, function () {
                        $location.url("/");
                    });
                }
            ]
        },
        title: "Contents by Album Artist",
        special: {
            section: "artist"
        }
    });

    $routeProvider.when("/artist/:artist/:album", {
        controller: "AlbumViewController",
        templateUrl: templatePath + "/album-view.html",
        resolve: {
            Resolved: ["SearchService", "$location", "$route", "$filter",
                function (SearchService, $location, $route, $filter) {
                    var artist = decodeUriPlus($route.current.params.artist),
                        album = decodeUriPlus($route.current.params.album);

                    $route.current.title = String.prototype.concat(
                        $filter("albumFilter")(album) + " by " +
                        $filter("artistFilter")(artist)
                    );

                    return SearchService.tracks({ artist: artist, album: album, limit: -1 }, 0).then(function (response) {
                        return response;
                    }, function () {
                        $location.url("/");
                    });

                }
            ]
        },
        title: "Album",
        special: {
            section: "albums"
        }
    });

    $routeProvider.when("/albums/", {
        templateUrl: templatePath + "/albums-view.html",
        controller: "AllAlbumsViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                var q = $location.search().q;
                return SearchService.albums({ q: q, compilations: 0 }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        title: "Albums",
        special: {
            section: "albums"
        }
    });

    $routeProvider.when("/compilations/", {
        templateUrl: templatePath + "/albums-view.html",
        controller: "AllCompilationsViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                var q = $location.search().q;
                return SearchService.albums({ q: q, compilations: 1 }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        title: "Compilations",
        special: {
            section: "compilations"
        }
    });

    $routeProvider.when("/genres/", {
        templateUrl: templatePath + "/genres-view.html",
        controller: "AllGenresViewController",
        resolve: {
            Resolved: ["SearchService", "$location", function (SearchService, $location) {
                var q = $location.search().q;
                return SearchService.genres({ q: q }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        },
        title: "Genres",
        special: {
            section: "genres"
        }
    });

    $routeProvider.when("/genre/:genre", {
        templateUrl: templatePath + "/single-genre-view.html",
        controller: "GenreViewController",
        resolve: {
            Header: ["$route", "HeadersService", function ($route, HeadersService) {
                var genre = decodeUriPlus($route.current.params.genre);
                return HeadersService.genre(genre).then(function (response) {
                    return response.data;
                });
            }],
            Resolved: ["SearchService", "$location", "$route", "$filter",
                function (SearchService, $location, $route, $filter) {

                    var genre = decodeUriPlus($route.current.params.genre);

                    $route.current.title = $filter("genreFilter")(genre);

                    return SearchService.tracks({ genre: genre }, 0).then(function (response) {
                        return response;
                    }, function () {
                        $location.url("/");
                    });
                }
            ]
        },
        title: "Track Genres",
        special: {
            section: "genres"
        }
    });



    $routeProvider.when("/tracks", {
        controller: "TracksViewController",
        templateUrl: templatePath + "/tracks-view.html",
        resolve: {
            Resolved: ["SearchService", "$location",
                function (SearchService, $location) {
                    var q = $location.search().q,
                        s = $location.search().s;
                    return SearchService.tracks({ q: q, s: s }, 0).then(function (response) {
                        return response;
                    }, function () {
                        $location.url("/");
                    });

                }
            ]
        },
        title: "Tracks",
        special: {
            section: "tracks"
        }
    });

    //$routeProvider.otherwise({
    //    redirectTo: "/artists/"
    //});
}]);