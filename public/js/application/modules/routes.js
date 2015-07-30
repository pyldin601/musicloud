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
        }
    });

    $routeProvider.when("/artist/:artist", {
        templateUrl: templatePath + "/artist-view.html",
        controller: "ArtistViewController",
        resolve: {
            Resolved: ["SearchService", "$route", "$location", function (SearchService, $route, $location) {
                return SearchService.tracks({ artist_id: $route.current.params.artist }, 0).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        }
    });

    $routeProvider.otherwise({
        redirectTo: "/artists/"
    });
}]);