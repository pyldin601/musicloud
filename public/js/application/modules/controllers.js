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