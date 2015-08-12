/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("ArtistViewController", [
    "Resolved", "Header", "$scope", "MonitorSongs", "SyncService", "Library",
    function (Resolved, Header, $scope, MonitorSongs, SyncService, Library) {

        $scope.header = Header;
        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.tracks_selected = [];

        $scope.albums = [];

        $scope.group = function () {
            $scope.albums = Library.groupAlbums($scope.tracks);
        };

        $scope.$watch("tracks", $scope.group, true);

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

    }
]);

homecloud.controller("GenreViewController", [
    "Resolved", "Header", "SearchService", "SyncService", "Library", "$scope", "MonitorSongs", "$routeParams",
    function (Resolved, Header, SearchService, SyncService, Library, $scope, MonitorSongs, $routeParams) {

        var genre = decodeUriPlus($routeParams.genre);

        $scope.header = Header;
        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.tracks_selected = [];

        $scope.albums = [];

        $scope.busy = false;
        $scope.end = false;

        $scope.fetch = SearchService.tracks.curry({ genre: genre });

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

        $scope.$watch("tracks", $scope.group, true);

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);


    }
]);

homecloud.controller("AlbumViewController", [
    "Resolved", "$scope", "SyncService", "MonitorSongs", "$location",
    function (Resolved, $scope, SyncService, MonitorSongs, $location) {

        $scope.tracks = SyncService.tracks(Resolved.tracks);
        $scope.tracks_selected = [];
        $scope.album = {};

        $scope.readAlbum = function () {

            var genres = $scope.tracks.map(field("track_genre")).distinct(),
                years = $scope.tracks.map(field("track_year")).distinct();

            if ($scope.tracks.length == 0) {
                $location.url("/albums/");
                return;
            }

            $scope.album = {
                album_title: $scope.tracks.map(field("track_album")).reduce(or, ""),
                album_url: $scope.tracks.map(field("album_url")).reduce(or, ""),
                album_artist: $scope.tracks.map(field("album_artist")).reduce(or, ""),
                cover_id: $scope.tracks.map(field("middle_cover_id")).reduce(or, null),
                album_year: (years.length == 0) ? "-" :
                    (years.length == 1) ? years.first() :
                    (years.length == 2) ? years.join(", ") :
                    (years.min() + " - " + years.max()),
                album_genre: (genres.length == 0) ? "" :
                    (genres.length == 1) ? genres.first() :
                    (genres.length == 2) ? genres.join(", ") :
                    "Multiple Genres",
                length: $scope.tracks.map(function (t) {
                    return parseFloat(t.length)
                }).reduce(sum, 0),
                is_various: $scope.tracks.any(function (t) {
                    return t.track_artist !== t.album_artist
                })
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
        $scope.fetch = SearchService.tracks.curry({q: $location.search().q});

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
            SearchService.artists({q: $location.search().q}, $scope.artists.length).success(function (data) {
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
            SearchService.albums({q: $location.search().q}, $scope.albums.length).success(function (data) {
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
            SearchService.genres({q: $location.search().q}, $scope.genres.length).success(function (data) {
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


homecloud.controller("SearchController", ["$scope", "SearchService", "$timeout", "SyncService", "$q",

    function ($scope, SearchService, $timeout, SyncService, $q) {

        var promise,
            delay = 200,
            canceller;

        $scope.query = "";
        $scope.results = {};

        $scope.$watch("query", function (newValue) {
            if (!newValue) {
                $scope.reset();
                return;
            }
            $timeout.cancel(promise);
            promise = $timeout($scope.search, delay);
        });

        $scope.search = function () {

            if (canceller) canceller.resolve();

            canceller = $q.defer();

            $scope.results.artists_busy = true;
            $scope.results.albums_busy = true;
            $scope.results.tracks_busy = true;


            SearchService.tracks({ q: $scope.query, limit: 5 }, 0, { timeout: canceller.promise }).success(function (response) {
                $scope.results.tracks = SyncService.tracks(response.tracks);
                $scope.results.tracks_busy = false;
            });

            SearchService.artists({ q: $scope.query, limit: 5 }, 0, { timeout: canceller.promise }).success(function (response) {
                $scope.results.artists = response.artists;
                $scope.results.artists_busy = false;
            });

            SearchService.albums({ q: $scope.query, limit: 5 }, 0, { timeout: canceller.promise }).success(function (response) {
                $scope.results.albums = response.albums;
                $scope.results.albums_busy = false;
            });

        };

        $scope.$on("$routeChangeSuccess", function () {
            $scope.reset();
        });

        $scope.reset = function () {
            $scope.query = "";
            $scope.results = {
                artists: [],
                albums: [],
                tracks: [],
                artists_busy: false,
                albums_busy: false,
                tracks_busy: false
            };
        };

    }
]);