var homecloud = angular.module("HomeCloud");

homecloud.run(["$rootScope", "ModalWindow", function ($rootScope, ModalWindow) {
    $rootScope.editMetadata = function (coll) {
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
}]);

homecloud.controller("MetadataController", ["$scope", "TrackService", "SyncService", "$filter",
    function ($scope, TrackService, SyncService, $filter) {

        var songs        = $scope.songs,
            artists_list = songs.map(field("album_artist")).distinct(),
            albums_list  = songs.map(field("track_album")).distinct(),
            cover_url    = songs.map(field("middle_cover_id")).reduce(or),

            is_compilation = songs.all(field("is_compilation")),

            unmodified  = "[unmodified]";

        $scope.fields = {
            track_title: "",
            track_artist: "",
            track_album: "",
            album_artist: "",
            track_number: "",
            disc_number: "",
            track_genre: "",
            track_year: ""
        };

        $scope.flags = {
            is_compilation: is_compilation
        };

        $scope.current_cover = cover_url;

        $scope.selected = {
            artists: (artists_list.length == 1) ? $filter("artistFilter")(artists_list.first()) :
                     "" + artists_list.length + " artist(s)",
            songs: (songs.length == 1) ? $filter("getTitle")(songs.first()) :
                   (albums_list.length == 1) ? albums_list.first() :
                   "" + songs.length + " song(s)"
        };

        $scope.load = function () {
            for (var i = 0; i < songs.length; i += 1) {
                for (var key in $scope.fields) if ($scope.fields.hasOwnProperty(key)) {
                    if (i == 0) {
                        $scope.fields[key] = songs[0][key];
                    } else if (songs[0][key] !== songs[i][key]) {
                        $scope.fields[key] = unmodified;
                    }
                }
            }
        };

        $scope.save = function () {

            var song_id = songs.map(function (e) { return e.id }).join(","),
                submission = { song_id: song_id, metadata: {} };

            for (var key in $scope.fields) if ($scope.fields.hasOwnProperty(key)) {
                if ($scope.fields[key] !== unmodified) {
                    submission.metadata[key] = $scope.fields[key];
                }
            }

            if ($scope.flags.is_compilation !== is_compilation) {
                submission.metadata.is_compilation = $scope.flags.is_compilation
            }

            TrackService.edit(submission).success(function (updated) {
                SyncService.tracks(updated.tracks);
                $scope.closeThisWindow();
            });

        };

        $scope.load();

    }
]);