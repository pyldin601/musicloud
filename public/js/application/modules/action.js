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
                    TrackService.unlink(song_ids).success();
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
                })
            }
        };
    }]);

    homecloud.factory("MonitorSongs", [function () {
        return function (coll, scope) {
            scope.$on("songs.deleted", function (e, data) {
                for (var i = 0; i < coll.length; i += 1) {
                    for (var j = 0; j < data.length; j += 1) {
                        if (coll[i].id == data[j].id) {
                            console.log(coll[i].track_title);
                            coll.splice(i, 1);
                            break;
                        }
                    }
                }
                scope.$applyAsync();
            });
        }
    }]);

})();
