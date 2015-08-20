/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.run(["$rootScope", "$filter", function ($rootScope, $filter) {

    $rootScope.selectedTracksMenu = function (selection) {
        var defaultMenu = [
            {
                type: "item",
                text: "Edit metadata",
                action: function () {
                    $rootScope.action.editSongs(selection);
                }
            },
            {
                type: "item",
                text: "Delete tracks",
                action: function () {
                    $rootScope.action.deleteSongs(selection);
                }
            }
        ];

        switch (selection.length) {

            case 0:
                return null;

            case 1:
                defaultMenu.push({
                    type: "divider"
                });
                defaultMenu.push({
                    type: "item",
                    text: "Show all by <b>" + $filter("artistFilter")(selection[0].album_artist) + "</b>",
                    href: selection[0].artist_url
                });
                defaultMenu.push({
                    type: "item",
                    text: "Show all from <b>" + $filter("albumFilter")(selection[0].track_album) + "</b>",
                    href: selection[0].album_url
                });
                if (selection[0].track_genre) {
                    defaultMenu.push({
                        type: "item",
                        text: "Show all of <b>" + selection[0].track_genre + "</b>",
                        href: selection[0].genre_url
                    });
                }
                break;

            default:
                break;

        }

        return defaultMenu;

    };

}]);

MusicLoud.controller("ArtistViewController", [
    "Resolved", "Header", "$scope", "MonitorSongs", "MonitorGroups", "SyncService", "$routeParams", "SearchService", "GroupingService",
    function (Resolved, Header, $scope, MonitorSongs, MonitorGroups, SyncService,  $routeParams, SearchService, GroupingService) {

        var artist = decodeUriPlus($routeParams.artist),
            gs = GroupingService("track_album");

        $scope.header = Header;
        $scope.tracks = Resolved;
        $scope.tracks_selected = [];
        $scope.albums = gs.getGroups();
        $scope.busy = false;
        $scope.end = false;

        $scope.fetch = SearchService.tracks.curry({ artist: artist });

        gs.addItems(Resolved);

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).then(function (data) {
                if (data.length > 0) {
                    array_add(data, $scope.tracks);
                    gs.addItems(data);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);
        MonitorGroups(gs, $scope);


    }
]);

MusicLoud.controller("GenreViewController", [
    "Resolved", "Header", "SearchService", "SyncService", "$scope", "MonitorSongs", "MonitorGroups", "$routeParams", "GroupingService",
    function (Resolved, Header, SearchService, SyncService, $scope, MonitorSongs, MonitorGroups, $routeParams, GroupingService) {

        var genre = decodeUriPlus($routeParams.genre),
            gs = GroupingService("track_album");

        $scope.header = Header;
        $scope.tracks = Resolved;
        $scope.tracks_selected = [];
        $scope.albums = gs.getGroups();
        $scope.busy = false;
        $scope.end = false;

        $scope.fetch = SearchService.tracks.curry({ genre: genre });

        gs.addItems(Resolved);

        $scope.load = function () {
            $scope.busy = true;
            $scope.fetch($scope.tracks.length).then(function (data) {
                if (data.length > 0) {
                    array_add(data, $scope.tracks);
                    gs.addItems(data);
                    $scope.busy = false;
                } else {
                    $scope.end = true;
                }
            })
        };

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);
        MonitorGroups(gs, $scope);

    }
]);

MusicLoud.controller("AlbumViewController", [
    "Resolved", "$scope", "SyncService", "MonitorSongs", "$location",
    function (Resolved, $scope, SyncService, MonitorSongs, $location) {

        $scope.tracks = Resolved;
        $scope.tracks_selected = [];
        $scope.album = {};
        $scope.tracks_selected = [];
        $scope.fetch = null;

        $scope.readAlbum = function () {

            if ($scope.tracks.length == 0) {
                $location.url("/");
                return;
            }

            $scope.album = {
                album_title:    aggregateAlbumTitle($scope.tracks),
                album_url:      $scope.tracks.map(field("album_url")).reduce(or, ""),
                album_artist:   $scope.tracks.map(field("album_artist")).reduce(or, ""),
                cover_id:       $scope.tracks.map(field("middle_cover_id")).reduce(or, null),
                artist_url:     $scope.tracks.map(field("artist_url")).reduce(or),
                album_year:     groupYears($scope.tracks),
                album_genre:    groupGenres($scope.tracks),
                length:         aggregateDuration($scope.tracks),
                discs_count:    $scope.tracks.map(field("disk_number")).distinct().length,
                is_various:     $scope.tracks.any(function (t) {
                                    return t.track_artist !== t.album_artist
                                })
            };

        };

        MonitorSongs($scope.tracks, $scope);
        MonitorSongs($scope.tracks_selected, $scope);

        $scope.$watch("tracks", $scope.readAlbum, true);


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

        $scope.contextMenu = [
            {
                text: "Edit metadata",
                action: function () {
                    $scope.action.editSongs($scope.tracks_selected);
                }
            },
            {
                text: "Delete from library",
                action: function () {
                    $scope.action.deleteSongs($scope.tracks_selected);
                }
            }
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