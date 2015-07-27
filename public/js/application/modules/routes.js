/**
 * Created by Roman on 27.07.2015.
 */

var mediacloud = angular.module("HomeCloud");

mediacloud.config(["$routeProvider", "$locationProvider", function ($routeProvider, $locationProvider) {
    var templatePath = "/public/js/application/templates";

    //$locationProvider.html5Mode(true).hashPrefix("!");

    $routeProvider.when("/artists/", {
        templateUrl: templatePath + "/artists-view.html",
        controller: "AllArtistsViewController",
        resolve: {
            AllArtistsContent: ["SearchService", "$location", function (SearchService, $location) {
                return SearchService.artists(0, "").then(function (response) {
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
            ArtistContent: ["LibraryService", "$route", "$location", function (LibraryService, $route, $location) {
                return LibraryService.tracksByArtist($route.current.params.artist).then(function (response) {
                    return response.data;
                }, function () {
                    $location.url("/");
                });
            }]
        }
    });
    $routeProvider.otherwise({
        redirectTo: "/"
    });
}]);