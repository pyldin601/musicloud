/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("ArtistViewController", [
    "ArtistContent", "SearchService", "$scope", "$routeParams",
    function (ArtistContent, SearchService, $scope, $routeParams) {

        $scope.artist = $routeParams.artist || "";
        $scope.tracks = ArtistContent.tracks;
        $scope.tracks_selected = [];
        $scope.busy = false;
        $scope.end = false;

        $scope.$watch("albums", function (data) {
            console.log(data);
        });

        $scope.load = function () {
            $scope.busy = true;
            SearchService.tracks($scope.tracks.length, "").success(function (data) {
                if (data.tracks.length > 0) {
                    $scope.tracks = $scope.tracks.concat(data.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

        $scope.$watch("tracks_selected", function (n, o) {
            //console.log(n, o);
        });

    }
]);

homecloud.controller("AllArtistsViewController", [
    "AllArtistsContent", "SearchService", "$scope", function (AllArtistsContent, SearchService, $scope) {

        $scope.artists = AllArtistsContent.artists;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.artists($scope.artists.length, "").success(function (data) {
                if (data.artists.length > 0) {
                    $scope.artists = $scope.artists.concat(data.artists);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);