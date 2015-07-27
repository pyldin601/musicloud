/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("ArtistViewController", [
    "ArtistContent", "Library", "$scope", "$routeParams", function (ArtistContent, Library, $scope, $routeParams) {

        $scope.artist = $routeParams.artist;
        $scope.tracks = ArtistContent.tracks;
        $scope.albums = Library.groupAlbums($scope.tracks);

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