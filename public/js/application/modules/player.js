
var homecloud = angular.module("HomeCloud");

homecloud.run(["$rootScope", function ($rootScope) {

    var jFrame = $("<div>").appendTo("body");

    jFrame.jPlayer({
        ready: function(e) {

        },
        ended: function(e) {
            $rootScope.player.doPlayNext();
        },
        error: function(e) {
            $rootScope.player.doStop();
        },
        timeupdate: function(e) {
            $rootScope.player.playlist.position.duration = e.jPlayer.status.duration;
            $rootScope.player.playlist.position.position = e.jPlayer.status.currentTime;
            $rootScope.$digest();
        },
        progress: function(e) {  },
        swfPath: "/public/js/application/libs/jplayer/",
        supplied: "m4a, mp3",
        solution: "html, flash",
        volume: 1
    });

    $rootScope.player = {
        isLoaded: false,
        isPlaying: false,
        playlist: {
            tracks: [],
            index: null,
            track: null,
            position: {
                duration: 0,
                position: 0
            }
        },
        doPlay: function (track, playlist) {

            var format, file;

            if (playlist !== undefined && playlist !== $rootScope.player.playlist.tracks) {
                $rootScope.player.playlist.tracks = playlist;
            }

            $rootScope.player.playlist.track = track;

            switch (track.content_type) {
                case "audio/mp4":
                    format = "m4a";
                    break;
                default:
                    format = "mp3";
            }

            file = {};
            file[format] = "/content/track/" + track.id;

            jFrame.jPlayer("setMedia", file);

            $rootScope.player.isLoaded = true;

            jFrame.jPlayer("play");

            $rootScope.player.isPlaying = true;

        },
        doPlayPause: function () {



        },
        doStop: function () {

            jFrame.jPlayer("stop");

            $rootScope.player.isLoaded = false;
            $rootScope.player.isPlaying = false;
            $rootScope.player.playlist.track = null;
            $rootScope.player.playlist.tracks = [];

        },
        doPlayNext: function () {

            if ($rootScope.player.playlist.track === null) {
                return;
            }

            var index = $rootScope.player.playlist.tracks.indexOf($rootScope.player.playlist.track);

            if (index < $rootScope.player.playlist.tracks.length - 1) {
                $rootScope.player.doPlay($rootScope.player.playlist.tracks[index + 1])
            } else {
                $rootScope.player.doStop();
            }

        },
        doPlayPrev: function () {

            if ($rootScope.player.playlist.track === null) {
                return;
            }

            var index = $rootScope.player.playlist.tracks.indexOf($rootScope.player.playlist.track);

            if (index > 0) {
                $rootScope.player.doPlay($rootScope.player.playlist.tracks[index - 1])
            } else {
                $rootScope.player.doStop();
            }

        }
    }


}]);