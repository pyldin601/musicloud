
var homecloud = angular.module("HomeCloud");

homecloud.run(["$rootScope", "StatsService", function ($rootScope, StatsService) {

    var jFrame = $("<div>").appendTo("body");

    var player = {
        isLoaded: false,
        isPlaying: false,
        playlist: {
            tracks: [],
            track: null,
            fetch: null,
            position: {
                duration: 0,
                position: 0,
                load: 0
            }
        },
        doPlay: function (track, playlist, resolver) {

            if (playlist !== undefined && playlist !== player.playlist.tracks) {
                player.playlist.tracks = playlist;
            }

            if (resolver !== undefined) {
                player.playlist.fetch = resolver;
            }

            player.playlist.track = track;

            jFrame.jPlayer("setMedia", { mp3: "/file/" + track.file_id }).jPlayer("play");

            player.isLoaded = true;
            player.isPlaying = true;

        },
        doFetch: function () {
            if (!player.playlist.fetch) return;
            player.playlist.fetch(player.playlist.tracks.length).success(function (data) {
                if (data.tracks.length > 0) {
                    player.playlist.tracks = player.playlist.tracks.concat(data.tracks);
                } else {
                    player.playlist.fetch = null;
                }
            });
        },
        doPlayPause: function () {

            if (player.isPlaying) {
                player.isPlaying = false;
                jFrame.jPlayer("pause");
            } else {
                player.isPlaying = true;
                jFrame.jPlayer("play");
            }

        },
        doStop: function () {

            jFrame.jPlayer("stop").jPlayer("clearMedia");

            $rootScope.$applyAsync(function () {

                player.isLoaded = false;
                player.isPlaying = false;
                player.playlist.track = null;
                player.playlist.tracks = [];
                player.playlist.fetch = null;

                player.playlist.position = {
                    duration: 0,
                    position: 0,
                    load: 0
                };

            });

        },
        doSeek: function (position) {

            if (!player.isLoaded) return;

            jFrame.jPlayer("playHead", position);

        },
        doPlayNext: function () {

            if (player.playlist.track === null) {
                return;
            }

            var index = player.playlist.tracks.indexOf(player.playlist.track);

            if (index < player.playlist.tracks.length - 1) {
                player.doPlay(player.playlist.tracks[index + 1])
            } else {
                player.doStop();
            }

            if (index + 1 == player.playlist.tracks.length - 1 && player.playlist.fetch) {
                // If it's last try to fetch new tracks
                console.log("Fetching new tracks...");
                player.doFetch();
            }

        },
        doPlayPrev: function () {

            if (player.playlist.track === null) {
                return;
            }

            var index = player.playlist.tracks.indexOf(player.playlist.track);

            if (index > 0) {
                player.doPlay(player.playlist.tracks[index - 1])
            } else {
                player.doStop();
            }

        }
    };

    jFrame.jPlayer({
        ready: function () {
        },
        ended: function () {
            if (player.playlist.track) {
                StatsService.incrementPlays(player.playlist.track.id).success(function () {
                    player.playlist.track.playbacks_count += 1;
                });
            }
            player.doPlayNext();
        },
        error: function () {
            player.doStop();
        },
        timeupdate: function (e) {
            $rootScope.$applyAsync(function () {
                player.playlist.position.duration = e.jPlayer.status.duration;
                player.playlist.position.position = e.jPlayer.status.currentTime;
            });
        },
        swfPath: "/public/js/application/libs/jplayer/",
        supplied: "mp3",
        solution: "html, flash",
        volume: 1
    });

    $rootScope.player = player;


}]);