/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.run(["$rootScope", function ($rootScope) {

    $rootScope.selectedTracksMenu = function (selection) {
        var defaultMenu = [
            {
                type: 'item',
                text: '<i class="fa fa-pencil-square item-icon"></i> Edit metadata',
                action: function () {
                    $rootScope.action.editSongs(selection);
                }
            },
            {
                type: 'item',
                text: '<i class="fa fa-minus-square item-icon"></i> Delete track(s) completely',
                action: function () {
                    $rootScope.action.deleteSongs(selection);
                }
            }
        ];

        if (selection.length > 0) {

            if (selection[0].link_id) {
                defaultMenu.push({
                    type: 'item',
                    text: '<i class="fa fa-minus-square item-icon"></i> Delete track(s) from playlist',
                    action: function () {
                        $rootScope.playlistMethods.removeTracksFromPlaylist(selection)
                    }
                });
            }

        }

        switch (selection.length) {

            case 0:
                return null;

            case 1:
                defaultMenu.push({ type: 'divider' });
                if (selection[0].album_artist) {
                    defaultMenu.push({
                        type: 'item',
                        text: '<i class="fa fa-search item-icon"></i> Show all by <b>' + htmlToText(selection[0].album_artist) + '</b>',
                        href: selection[0]["artist_url"]
                    });
                }
                if (selection[0].track_album) {
                    defaultMenu.push({
                        type: 'item',
                        text: '<i class="fa fa-search item-icon"></i> Show all from <b>' + htmlToText(selection[0].track_album) + '</b>',
                        href: selection[0]["album_url"]
                    });
                }
                if (selection[0].track_genre) {
                    defaultMenu.push({
                        type: 'item',
                        text: '<i class="fa fa-search item-icon"></i> Show all by genre <b>' + htmlToText(selection[0].track_genre) + '</b>',
                        href: selection[0]["genre_url"]
                    });
                }
                break;

            default:
                break;

        }

        defaultMenu.push({ type: 'divider' });

        defaultMenu.push({
            type: 'sub',
            text: '<i class="fa fa-plus item-icon"></i> Add to playlist',
            data: $rootScope.playlists.map(function (playlist) {
                return {
                    type: 'item',
                    text: '<i class="fa fa-list item-icon"></i> ' + htmlToText(playlist.name),
                    action: function () {
                        $rootScope.playlistMethods.addTracksToPlaylist(playlist, selection);
                    }
                }
            })
        });

        return defaultMenu;

    };

}]);


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

MusicLoud.controller("PlaylistsController", ["$rootScope", "PlaylistService", function ($scope, PlaylistService) {

        $scope.playlists = [];
        $scope.playlistMethods = {
            reloadPlaylists: function () {
                PlaylistService.list().then(function (response) {
                    array_copy(response.data, $scope.playlists);
                });
            },
            createNewPlaylist: function () {
                var name = prompt("Please enter name for new playlist", "New playlist");
                if (name !== null && name !== "") {
                    PlaylistService.create(name).then(function (response) {
                        $scope.playlists.push(response.data);
                    }, function (response) {
                        alert (response.data.message);
                    })
                }
            },
            deletePlaylist: function (playlist) {
                if (confirm('Are you sure want to delete playlist "' + playlist.name + '"')) {
                    PlaylistService.remove(playlist);
                    $scope.$broadcast("playlist.deleted", playlist);
                    $scope.playlists.splice($scope.playlists.indexOf(playlist), 1);
                }
                return false;
            },
            addTracksToPlaylist: function (playlist, tracks) {
                PlaylistService.addTracks(playlist, tracks);
            },
            removeTracksFromPlaylist: function (tracks) {
                PlaylistService.removeTracks(tracks);
                $scope.$broadcast("playlist.songs.deleted", tracks.map(field("link_id")));
            }
        };

        $scope.playlistMethods.reloadPlaylists();

    }

]);

MusicLoud.controller("PlaylistController", [
    "$scope", "Resolved", "Playlist", "SyncKeeper", "$location",
    function ($scope, Resolved, Playlist, SyncKeeper, $location) {

        var syncKeeper = SyncKeeper($scope);

        $scope.tracks = Resolved;
        $scope.tracks_selected = [];
        $scope.fetch = null;

        syncKeeper  .songs($scope.tracks)
                    .songs($scope.tracks_selected)
                    .playlistSongs($scope.tracks)
                    .playlistSongs($scope.tracks_selected);

        $scope.$on("playlist.deleted", function (event, data) {
            if (data["id"] == Playlist["id"]) {
                $location.url("/");
            }
        });

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