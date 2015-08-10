/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("GroupViewController", [
    "Resolved", "SearchService", "SyncService", "Library", "$scope", "$location", "MonitorSongs",
    function (Resolved, SearchService, SyncService, Library, $scope, $location, MonitorSongs) {

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.tracks_selected = [];

        $scope.albums = [];

        $scope.busy = false;
        $scope.end = false;

        $scope.fetch = SearchService.tracks.curry($location.search());

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    array_add(SyncService.tracks(data.tracks), $scope.tracks);
                    Library.addToGroup($scope.albums, data.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

        $scope.group = function () {
            $scope.albums = Library.groupAlbums($scope.tracks);
        };

        $scope.$watch($scope.tracks, $scope.group, true);

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);


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

            var genres = $scope.tracks.map(field("track_genre")).distinct(),
                years  = $scope.tracks.map(field("track_year")).distinct();

            $scope.album = {
                album_title  : $scope.tracks.map(field("track_album")).reduce(or),
                album_artist : $scope.tracks.map(field("album_artist")).reduce(or),
                cover_id     : $scope.tracks.map(field("middle_cover_id")).reduce(or),
                album_year   : (years.length == 1) ? years.first() :
                               (years.length == 2) ? genres.join(", ") :
                               (genres.min() + " - " + genres.max()),
                album_genre  : (genres.length == 1) ? genres.first() :
                               (genres.length == 2) ? genres.join(", ") :
                               "Multiple Genres",
                length       : $scope.tracks.map(function (t) { return parseFloat(t.length) }).reduce(sum),
                is_various   : $scope.tracks.any(function (t) { return t.track_artist !== t.album_artist })
            };

        };

        $scope.tracks_selected = [];
        $scope.fetch = null;

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

        $scope.$watch("tracks", $scope.readAlbum, true);

    }
]);

homecloud.controller("TracksViewController", [
    "Resolved", "$scope", "SyncService", "MonitorSongs", "$location", "SearchService",
    function (Resolved, $scope, SyncService, MonitorSongs, $location, SearchService) {

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.busy = false;
        $scope.end = false;

        $scope.tracks_selected = [];
        $scope.fetch = SearchService.tracks.curry({ q : $location.search().q });

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    array_add(SyncService.tracks(data.tracks), $scope.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

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
                    array_add(data.artists, $scope.artists);
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
                    array_add(data.albums, $scope.albums);
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
                    array_add(data.genres, $scope.genres);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);
