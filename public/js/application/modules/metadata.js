var homecloud = angular.module("HomeCloud");

homecloud.run(["$rootScope", "ModalWindow", "SearchService", function ($rootScope, ModalWindow, SearchService) {
    SearchService.tracks(Empty, 0).success(function (data) {
        ModalWindow({
            template: templatePath + "/metadata-view.html",
            controller: "MetadataController",
            data: {
                songs: data.tracks
            }
        });
    });
}]);

homecloud.controller("MetadataController", ["$scope", "TrackService", "SyncService", "$filter",
    function ($scope, TrackService, SyncService, $filter) {

        var songs   = $scope.songs,
            artists = songs.map(function (e) { return e.album_artist }).distinct(),
            song_id = songs.map(function (e) { return e.id }).join(",");

        $scope.fields = {
            track_title: "",
            track_artist: "",
            track_album: "",
            album_artist: "",
            is_compilation: false
        };

        $scope.modify = {
            track_title: true,
            track_artist: true,
            track_album: true,
            album_artist: true,
            is_compilation: true
        };

        $scope.new_cover = null;

        $scope.selected = {
            artists: artists.length == 1 ? $filter("artistFilter")(artists.first()) : "" + artists.length + " artist(s)",
            songs: songs.length == 1 ? $filter("getTitle")(songs.first()) : "" + songs.length + " song(s)"
        };

        $scope.load = function () {
            for (var i = 0; i < songs.length; i += 1) {
                for (var key in $scope.fields) if ($scope.fields.hasOwnProperty(key)) {
                    if (i == 0) {
                        $scope.fields[key] = songs[i][key];
                        $scope.modify[key] = true;
                    } else if (songs[i] !== songs[i][key]) {
                        $scope.fields[key] = "";
                        $scope.modify[key] = false;
                    }
                }
            }
        };

        $scope.update = function () {

            var submission = { song_id: song_id };

            for (var key in $scope.fields) if ($scope.fields.hasOwnProperty(key)) {
                if ($rootScope.modify[key]) {
                    submission[key] = $scope.fields[key];
                }
            }

            TrackService.edit(submission).success(function (updated) {
                SyncService.tracks(updated.tracks);
                $scope.closeThisWindow();
            });

        };

        $scope.load();

    }
]);