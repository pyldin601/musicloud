/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.controller("ArtistViewController", [
    "Resolved", "Header", "$scope", "MonitorSongs", "SyncService", "Library",
    function (Resolved, Header, $scope, MonitorSongs, SyncService, Library) {

        $scope.header = Header;
        $scope.tracks = Resolved;
        $scope.tracks_selected = [];

        $scope.genre = groupGenres($scope.tracks);

        $scope.albums = [];

        $scope.group = function () {
            $scope.albums = Library.groupAlbums($scope.tracks);
        };

        $scope.$watch("tracks", $scope.group, true);

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

        $scope.menuOptions = [
            ["Edit info", function () {
                $scope.action.editSongs($scope.tracks_selected)
            }],
            ["Delete tracks", function () {
                $scope.action.deleteSongs($scope.tracks_selected)
            }]
        ];

    }
]);

MusicLoud.controller("GenreViewController", [
    "Resolved", "Header", "SearchService", "SyncService", "Library", "$scope", "MonitorSongs", "$routeParams",
    function (Resolved, Header, SearchService, SyncService, Library, $scope, MonitorSongs, $routeParams) {

        var genre = decodeUriPlus($routeParams.genre);

        $scope.header = Header;
        $scope.tracks = Resolved;
        $scope.tracks_selected = [];

        $scope.albums = [];

        $scope.busy = false;
        $scope.end = false;

        $scope.fetch = SearchService.tracks.curry({ genre: genre });

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).then(function (data) {
                if (data.length > 0) {
                    array_add(data, $scope.tracks);
                    Library.addToGroup($scope.albums, data);
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

        $scope.menuOptions = [
            ["Edit info", function () {
                $scope.action.editSongs($scope.tracks_selected)
            }],
            ["Delete tracks", function () {
                $scope.action.deleteSongs($scope.tracks_selected)
            }]
        ];

    }
]);

MusicLoud.controller("AlbumViewController", [
    "Resolved", "$scope", "SyncService", "MonitorSongs", "$location",
    function (Resolved, $scope, SyncService, MonitorSongs, $location) {

        $scope.tracks = Resolved;
        $scope.tracks_selected = [];
        $scope.album = {};

        $scope.readAlbum = function () {

            if ($scope.tracks.length == 0) {
                $location.url("/");
                return;
            }

            $scope.album = {
                album_title: aggregateAlbumTitle($scope.tracks),
                album_url: $scope.tracks.map(field("album_url")).reduce(or, ""),
                album_artist: $scope.tracks.map(field("album_artist")).reduce(or, ""),
                cover_id: $scope.tracks.map(field("middle_cover_id")).reduce(or, null),
                album_year: groupYears($scope.tracks),
                album_genre: groupGenres($scope.tracks),
                length: aggregateDuration($scope.tracks),
                discs_count: $scope.tracks.map(field("disk_number")).distinct().length,
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

        $scope.menuOptions = [
            ["Edit info", function () {
                $scope.action.editSongs($scope.tracks_selected)
            }],
            ["Delete tracks", function () {
                $scope.action.deleteSongs($scope.tracks_selected)
            }]
        ];

    }
]);

MusicLoud.controller("TracksViewController", [
    "Resolved", "$scope", "SyncService", "MonitorSongs", "$location", "SearchService",
    function (Resolved, $scope, SyncService, MonitorSongs, $location, SearchService) {

        $scope.tracks = Resolved;
        $scope.busy = false;
        $scope.end = false;

        $scope.tracks_selected = [];
        $scope.fetch = SearchService.tracks.curry({q: $location.search().q});

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).then(function (data) {
                if (data.length > 0) {
                    array_add(data, $scope.tracks);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

        $scope.menuOptions = [
            ["Edit info", function () {
                $scope.action.editSongs($scope.tracks_selected)
            }],
            ["Delete tracks", function () {
                $scope.action.deleteSongs($scope.tracks_selected)
            }]
        ];

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

    }
]);

MusicLoud.controller("AllArtistsViewController", [
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.artists = Resolved;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.artists({q: $location.search().q}, $scope.artists.length).then(function (artists) {
                if (artists.length > 0) {
                    array_add(artists, $scope.artists);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);

MusicLoud.controller("AllAlbumsViewController", [
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.albums = Resolved;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.albums({q: $location.search().q, compilations: 1}, $scope.albums.length).then(function (albums) {
                if (albums.length > 0) {
                    array_add(albums, $scope.albums);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);
//AllCompilationsViewController
MusicLoud.controller("AllCompilationsViewController", [
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.albums = Resolved;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.albums({q: $location.search().q, compilations: 1}, $scope.albums.length).then(function (albums) {
                if (albums.length > 0) {
                    array_add(albums, $scope.albums);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);

MusicLoud.controller("AllGenresViewController", [
    "Resolved", "SearchService", "$scope", "$location", function (Resolved, SearchService, $scope, $location) {

        $scope.genres = Resolved;
        $scope.busy = false;
        $scope.end = false;

        $scope.load = function () {
            $scope.busy = true;
            SearchService.genres({q: $location.search().q}, $scope.genres.length).then(function (genres) {
                if (genres.length > 0) {
                    array_add(genres, $scope.genres);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

    }
]);


MusicLoud.controller("SearchController", ["$scope", "SearchService", "$timeout", "SyncService", "$q",

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

            SearchService.tracks({ q: $scope.query, limit: 15 }, 0, { timeout: canceller.promise }).then(function (response) {
                $scope.results.tracks = response;
                $scope.results.tracks_busy = false;
            });

            SearchService.artists({ q: $scope.query, limit: 15 }, 0, { timeout: canceller.promise }).then(function (response) {
                $scope.results.artists = response;
                $scope.results.artists_busy = false;
            });

            SearchService.albums({ q: $scope.query, limit: 15 }, 0, { timeout: canceller.promise }).then(function (response) {
                $scope.results.albums = response;
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