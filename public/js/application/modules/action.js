/**
 * Created by roman on 09.08.15.
 */

(function () {

    var homecloud = angular.module("HomeCloud");

    homecloud.run(["$rootScope", "TrackService", "ModalWindow", function ($rootScope, TrackService, ModalWindow) {
        $rootScope.action = {
            deleteSongs: function (coll) {

                var song_ids;

                if (!coll || coll.length == 0)
                    return;

                song_ids = coll.map(field("id")).join(",");

                if (confirm("Are you sure want to delete selected songs?")) {
                    $rootScope.$broadcast("songs.deleted", coll);
                    TrackService.unlink({ song_id: song_ids });
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
                    }
                });

            }
        };
    }]);

    homecloud.factory("MonitorSongs", ["$rootScope", function ($rootScope) {
        return function (coll, scope) {
            scope.$on("songs.deleted", function (e, data) {
                scope.$applyAsync(function () {
                    for (var j = 0; j < data.length; j += 1) {
                        //if ($rootScope.player.isPlaying &&
                        //    $rootScope.player.playlist.track.id == data[j].id) {
                        //    $rootScope.player.doStop();
                        //}
                        for (var i = coll.length - 1; i >= 0; i -= 1) {
                            if (coll[i].id === data[j].id) {
                                coll.splice(i, 1);
                                break;
                            }
                        }
                    }
                });
            });
            scope.$on("songs.updated", function (e, data) {
                scope.$applyAsync(function () {
                    for (var j = 0; j < data.length; j += 1) {
                        for (var i = coll.length - 1; i >= 0; i += 0) {
                            if (coll[i].id === data[j].id) {
                                angular.copy(data[j], coll[i]);
                                break;
                            }
                        }
                    }
                });
            });
        }
    }]);

})();
