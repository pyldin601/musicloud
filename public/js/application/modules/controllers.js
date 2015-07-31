/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("ArtistViewController", [
    "Resolved", "SearchService", "SyncService", "Library", "$scope", "$routeParams",
    function (Resolved, SearchService, SyncService, Library, $scope, $routeParams) {

        $scope.artist = $routeParams.artist;
        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.albums = [];

        $scope.tracks_selected = [];
        $scope.busy = false;
        $scope.end = false;

        $scope.albums = Library.groupAlbums($scope.tracks);

        $scope.fetch = SearchService.tracks.curry({ artist_id: $scope.artist });

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    $scope.tracks = $scope.tracks.concat(SyncService.tracks(data.tracks));
                    Library.addToGroup($scope.albums, data.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);


homecloud.controller("AlbumViewController", [
    "Resolved", "SearchService", "SyncService", "Library", "$scope", "$routeParams",
    function (Resolved, SearchService, SyncService, Library, $scope, $routeParams) {

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.albums = [];

        $scope.tracks_selected = [];
        $scope.busy = false;
        $scope.end = false;

        $scope.albums = Library.groupAlbums($scope.tracks);
        $scope.fetch = SearchService.tracks.curry({ album_id: $routeParams.album });

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    $scope.tracks = $scope.tracks.concat(SyncService.tracks(data.tracks));
                    Library.addToGroup($scope.albums, data.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);

// GroupViewController
homecloud.controller("GroupViewController", [
    "Resolved", "SearchService", "SyncService", "Library", "$scope", "$location",
    function (Resolved, SearchService, SyncService, Library, $scope, $location) {

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.albums = [];

        $scope.tracks_selected = [];
        $scope.busy = false;
        $scope.end = false;

        $scope.albums = Library.groupAlbums($scope.tracks);
        $scope.fetch = SearchService.tracks.curry($location.search());

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    $scope.tracks = $scope.tracks.concat(SyncService.tracks(data.tracks));
                    Library.addToGroup($scope.albums, data.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);

homecloud.controller("GenreViewController", [
    "Resolved", "SearchService", "SyncService", "Library", "$scope", "$routeParams",
    function (Resolved, SearchService, SyncService, Library, $scope, $routeParams) {

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.albums = [];

        $scope.tracks_selected = [];
        $scope.busy = false;
        $scope.end = false;

        $scope.albums = Library.groupAlbums($scope.tracks);
        $scope.fetch = SearchService.tracks.curry({ genre_id: $routeParams.genre });

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    $scope.tracks = $scope.tracks.concat(SyncService.tracks(data.tracks));
                    Library.addToGroup($scope.albums, data.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);

homecloud.controller("AllArtistsViewController", [
    "Resolved", "SearchService", "$scope", function (Resolved, SearchService, $scope) {

        $scope.artists = Resolved.artists;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.artists(Empty, $scope.artists.length).success(function (data) {
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

homecloud.controller("AllAlbumsViewController", [
    "Resolved", "SearchService", "$scope", function (Resolved, SearchService, $scope) {

        $scope.albums = Resolved.albums;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.albums(Empty, $scope.albums.length).success(function (data) {
                if (data.albums.length > 0) {
                    $scope.albums = $scope.albums.concat(data.albums);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);

homecloud.controller("AllGenresViewController", [
    "Resolved", "SearchService", "$scope", function (Resolved, SearchService, $scope) {

        $scope.genres = Resolved.genres;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.genres(Empty, $scope.genres.length).success(function (data) {
                if (data.genres.length > 0) {
                    $scope.genres = $scope.genres.concat(data.genres);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);