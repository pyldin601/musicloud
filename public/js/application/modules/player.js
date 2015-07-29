
var homecloud = angular.module("HomeCloud");

homecloud.run(["$rootScope", function ($rootScope) {

    var jFrame = $("<div>").appendTo("body");

    var player = {
        isLoaded: false,
        isPlaying: false,
        playlist: {
            tracks: [],
            track: null,
            fetcher: null,
            position: {
                duration: 0,
                position: 0,
                load: 0
            }
        },
        doPlay: function (track, playlist, resolver) {

            if (playlist !== undefined && playlist !== $rootScope.player.playlist.tracks) {
                $rootScope.player.playlist.tracks = playlist;
            }

            if (resolver !== undefined) {
                $rootScope.player.playlist.fetcher = resolver;
            }

            $rootScope.player.playlist.track = track;

            jFrame.jPlayer("setMedia", { mp3: "/file/" + track.file_id }).jPlayer("play");

            $rootScope.player.isLoaded = true;
            $rootScope.player.isPlaying = true;

        },
        doFetch: function () {
            if (!player.playlist.fetcher) return;
            player.playlist.fetcher(player.playlist.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    player.playlist.tracks = player.playlist.tracks.concat(data.tracks);
                } else {
                    player.playlist.fetcher = null;
                }
            });
        },
        doPlayPause: function () {

            if ($rootScope.player.isPlaying) {
                $rootScope.player.isPlaying = false;
                jFrame.jPlayer("pause");
            } else {
                $rootScope.player.isPlaying = true;
                jFrame.jPlayer("play");
            }

        },
        doStop: function () {

            jFrame.jPlayer("stop").jPlayer("clearMedia");

            $rootScope.$applyAsync(function () {

                $rootScope.player.isLoaded = false;
                $rootScope.player.isPlaying = false;
                $rootScope.player.playlist.track = null;
                $rootScope.player.playlist.tracks = [];
                $rootScope.player.playlist.fetcher = null;

                $rootScope.player.playlist.position = {
                    duration: 0,
                    position: 0,
                    load: 0
                };

            });

        },
        doSeek: function (position) {

            if (!$rootScope.player.isLoaded) return;

            jFrame.jPlayer("playHead", position);

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

            if (index + 1 == player.playlist.tracks.length - 1 && player.playlist.fetcher) {
                // If it's last try to fetch new tracks
                console.log("Fetching new tracks...");
                player.doFetch();
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
    };

    jFrame.jPlayer({
        ready: function () {
        },
        ended: function () {
            $rootScope.player.doPlayNext();
        },
        error: function () {
            $rootScope.player.doStop();
        },
        timeupdate: function (e) {
            $rootScope.$applyAsync(function () {
                $rootScope.player.playlist.position.duration = e.jPlayer.status.duration;
                $rootScope.player.playlist.position.position = e.jPlayer.status.currentTime;
            });
        },
        swfPath: "/public/js/application/libs/jplayer/",
        supplied: "mp3",
        solution: "html, flash",
        volume: 1
    });

    $rootScope.player = player;


}]);