/**
 * Created by Roman on 27.07.2015.
 */

var mediacloud = angular.module("HomeCloud");

mediacloud.config(["$routeProvider", function ($routeProvider) {
    var templatePath = "/public/js/application/templates";
    $routeProvider.when("/", {
        template: "Init"
    });
    $routeProvider.when("/artist/:artist", {
        templateUrl: templatePath + "/artist-view.html",
        controller: "ArtistViewController",
        resolve: {
            ArtistContent: ["LibraryService", "$route", "$location", function (LibraryService, $route, $location) {
                var promise = LibraryService.tracksByArtist($route.current.params.artist);
                promise.error(function () {
                    $location.url("/");
                });
                return promise;
            }]
        }
    });
    $routeProvider.otherwise({
        redirectTo: "/"
    });
}]);