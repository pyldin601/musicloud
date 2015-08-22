/**
 * Created by roman on 09.08.15.
 */

(function () {

    var MusicLoud = angular.module("MusicLoud");

    MusicLoud.run(["$rootScope", "TrackService", "ModalWindow", function ($rootScope, TrackService, ModalWindow) {
        $rootScope.action = {
            deleteSongs: function (coll) {

                var song_ids;

                if (!coll || coll.length == 0)
                    return;

                if (confirm("Are you sure want to delete selected songs?")) {
                    song_ids = coll.map(field("id"));
                    $rootScope.$broadcast("songs.deleted", song_ids);
                    TrackService.unlink({ song_id: song_ids.join(",") });
                }

            },
            editSongs: function (coll) {

                if (!coll || coll.length == 0)
                    return;

                ModalWindow({
                    template: templatePath + "/metadata-view.html",
                    controller: "MetadataController",
                    data: {
                        songs: coll
                    },
                    closeOnEscape: true,
                    closeOnClick: true
                });

            }
        };
    }]);

    MusicLoud.factory("SyncKeeper", [function () {
        return function (scope) {
            var keeper = {
                songs: function (songs) {
                    scope.$on("songs.deleted", function (e, data) {
                        scope.$applyAsync(function () {
                            for (var j = 0; j < data.length; j += 1) {
                                for (var i = songs.length - 1; i >= 0; i -= 1) {
                                    if (songs[i]["id"] === data[j]) {
                                        songs.splice(i, 1);
                                        break;
                                    }
                                }
                            }
                        });
                    });
                    return keeper;
                },
                playlistSongs: function (songs) {
                    scope.$on("playlist.songs.deleted", function (e, data) {
                        scope.$applyAsync(function () {
                            for (var j = 0; j < data.length; j += 1) {
                                for (var i = songs.length - 1; i >= 0; i -= 1) {
                                    if (songs[i]["link_id"] === data[j]) {
                                        songs.splice(i, 1);
                                        break;
                                    }
                                }
                            }
                        });
                    });
                    return keeper;
                },
                groups: function (gs) {
                    scope.$on("songs.deleted", function (e, data) {
                        scope.$applyAsync(function () {
                            gs.removeItems("id", data);
                        });
                    });
                    return keeper;
                }
            };
            return keeper;
        }
    }]);



})();
