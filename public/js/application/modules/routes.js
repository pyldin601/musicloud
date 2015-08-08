/**
 * Created by Roman on 27.07.2015.
 */


var mediacloud = angular.module("HomeCloud");

mediacloud.config(["$routeProvider", function ($routeProvider) {

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
        title: "Artists",
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
        title: "Albums",
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
        title: "Genres",
        special: {
            section: "genres"
        }
    });

    $routeProvider.when("/tracks/grouped", {
        templateUrl: templatePath + "/grouped-view.html",
        controller: "GroupViewController",
        resolve: {
            Resolved: ["SearchService", "$location", "$route", "$filter",
                function (SearchService, $location, $route, $filter) {
                    var search  = $location.search(),
                        acc = "";

                    if (search.genre) {
                        acc = acc.concat($filter("genreFilter")(search.genre));
                    }

                    if (search.artist !== undefined && search.album !== undefined) {
                        if (acc.length > 0) {
                            acc = acc.concat(" in ");
                        }
                        acc = acc.concat(
                            $filter("albumFilter")(search.album) + " by " +
                            $filter("artistFilter")(search.artist)
                        )
                    } else if (search.artist !== undefined) {
                        if (acc.length > 0) {
                            acc = acc.concat(" in ");
                        }
                        acc = acc.concat($filter("artistFilter")(search.artist));
                    }

                    $route.current.title = acc;
                    return SearchService.tracks($location.search(), 0).then(function (response) {
                        return response.data;
                    }, function () {
                        $location.url("/");
                    });
                }
            ]
        },
        title: "My Library",
        special: {
            section: "tracks"
        }
    });

    $routeProvider.when("/album/:artist?/:album?", {
        controller: "AlbumViewController",
        templateUrl: templatePath + "/album-view.html",
        resolve: {
            Resolved: ["SearchService", "$location", "$route", "$filter",
                function (SearchService, $location, $route, $filter) {
                    var artist = $route.current.params.artist || "",
                        album  = $route.current.params.album || "";

                    $route.current.title = String.prototype.concat(
                        $filter("albumFilter")(album) + " by " +
                        $filter("artistFilter")(artist)
                    );

                    return SearchService.tracks({artist: artist, album: album, limit: -1}, 0).then(function (response) {
                        return response.data;
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

    //$routeProvider.otherwise({
    //    redirectTo: "/artists/"
    //});
}]);