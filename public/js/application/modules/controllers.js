/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

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

homecloud.controller("AlbumViewController", [
    "Resolved", "$scope", "SyncService", "MonitorSongs", "$location",
    function (Resolved, $scope, SyncService, MonitorSongs, $location) {

        if (Resolved.tracks.length == 0) {
            $location.url("/albums/");
            return;
        }

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.tracks_selected = [];
        $scope.album = {};

        $scope.readAlbum = function () {

            var genres = $scope.tracks.map(field("track_genre")).distinct();

            $scope.album = {
                album_title  : $scope.tracks.first().track_album,
                album_artist : $scope.tracks.first().album_artist,
                album_year   : $scope.tracks.map(field("track_year")).reduce(or),
                album_genre  : (genres.length == 1) ? genres.first()    :
                               (genres.length == 2) ? genres.join(", ") :
                               "Multiple Genres",
                cover_id     : $scope.tracks.map(field("middle_cover_id")).reduce(or),
                length       : $scope.tracks.map(function (t) { return parseFloat(t.length) }).reduce(sum),
                is_various   : $scope.tracks.any(function (t) { return t.track_artist !== t.album_artist })
            };
        };

        $scope.tracks_selected = [];
        $scope.fetch = null;

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

        $scope.readAlbum();

    }
]);

homecloud.controller("AllArtistsViewController", [
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.artists = Resolved.artists;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.artists({ q: $location.search().q }, $scope.artists.length).success(function (data) {
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
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.albums = Resolved.albums;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.albums({ q: $location.search().q }, $scope.albums.length).success(function (data) {
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
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.genres = Resolved.genres;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.genres({ q: $location.search().q }, $scope.genres.length).success(function (data) {
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