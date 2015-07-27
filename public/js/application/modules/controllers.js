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


    }
]);