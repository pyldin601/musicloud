/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("ArtistViewController", [
    "ArtistContent", "Library", "$scope", "$routeParams", function (ArtistContent, Library, $scope, $routeParams) {

        $scope.artist = $routeParams.artist;
        $scope.tracks = ArtistContent.data.tracks;
        $scope.albums = Library.groupAlbums($scope.tracks);

    }
]);