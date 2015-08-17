var homecloud = angular.module("HomeCloud");

homecloud.run(["$rootScope", "StatsService", "SyncService", "$cookies", "$timeout", "MonitorSongs",

    function ($rootScope, StatsService, SyncService, $cookies, $timeout, MonitorSongs) {

        var audio = document.createElement("audio"),

            jFrame = $("<div>").appendTo("body"),
            supportedFormats = {
                mp3: audio.canPlayType("audio/mp3") !== "",
                mp4: audio.canPlayType("audio/mp4") !== ""
            },
            timeout,
            player = {
                isLoaded: false,
                isPlaying: false,
                isBuffering: false,
                volume: 1,
                playlist: {
                    tracks: [],
                    track: null,
                    fetch: null,
                    position: {
                        duration: null,
                        position: null,
                        load: null
                    }
                },
                eventSkip: function () {
                    var percentPlayed;
                    if (!player.isPlaying)
                        return;

                    percentPlayed = 100 / player.playlist.position.duration * player.playlist.position.position;

                    if (percentPlayed > 10 && percentPlayed < 90) {
                        StatsService.incrementSkips(player.playlist.track);
                    }
                },
                doVolume: function (vol) {
                    jFrame.jPlayer("volume", vol);
                    player.volume = vol;
                    $cookies.put("volume", vol);
                },
                doPlay: function (track, playlist, resolver) {

                    $rootScope.$applyAsync(function () {

                        if (playlist !== undefined && playlist !== player.playlist.tracks) {
                            array_copy(playlist, player.playlist.tracks);
                        }

                        if (resolver !== undefined) {
                            player.playlist.fetch = resolver;
                        }

                        player.playlist.track = track;

                        jFrame.jPlayer("setMedia", {
                            mp3: (track.format == "mmp3") ? ("/file/" + track.file_id) : ("/preview/" + track.id)
                        }).jPlayer("play");

                        player.playlist.position.duration = track.length / 1000;

                        player.isLoaded = true;
                        player.isPlaying = true;

                        StatsService.scrobbleStart(track);

                    });

                },
                doFetch: function () {
                    if (!player.playlist.fetch)
                        return;

                    player.playlist.fetch(player.playlist.tracks.length).success(function (data) {
                        if (data.tracks.length > 0) {
                            player.playlist.tracks = player.playlist.tracks.concat(SyncService.tracks(data.tracks));
                        } else {
                            player.playlist.fetch = null;
                        }
                    });
                },
                doPlayPause: function () {

                    if (!player.isLoaded) {
                        return;
                    }

                    if (player.isPlaying) {
                        player.isPlaying = false;
                        jFrame.jPlayer("pause");
                    } else {
                        player.isPlaying = true;
                        jFrame.jPlayer("play");
                    }

                },
                doStop: function () {

                    $rootScope.$applyAsync(function () {

                        jFrame.jPlayer("clearMedia");

                        player.isLoaded = false;
                        player.isPlaying = false;
                        player.playlist.track = null;
                        array_copy([], player.playlist.tracks) ;
                        player.playlist.fetch = null;

                        player.playlist.position = {
                            duration: null,
                            position: null,
                            load: null
                        };

                    });

                },
                doSeek: function (percent) {

                    var timeIndex;

                    if (!(player.isLoaded && jFrame.data("jPlayer"))) return;

                    timeIndex = player.playlist.position.duration / 100 * percent;

                    if ( jFrame.data("jPlayer").status.paused ) {
                        jFrame.jPlayer( "pause", timeIndex );
                    } else {
                        jFrame.jPlayer( "play", timeIndex );
                    }

                },
                doPlayNext: function () {

                    if (!player.isLoaded) {
                        return;
                    }

                    player.eventSkip();

                    var index = player.playlist.tracks.indexOf(player.playlist.track);

                    if (index != -1 && index < player.playlist.tracks.length - 1) {

                        player.doPlay(player.playlist.tracks[index + 1]);

                        if (index + 1 == player.playlist.tracks.length - 1 && player.playlist.fetch) {
                            player.doFetch();
                        }

                    } else {

                        player.doStop();

                    }


                },
                doPlayPrev: function () {

                    if (!player.isLoaded) {
                        return;
                    }

                    player.eventSkip();

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
                    StatsService.incrementPlays(player.playlist.track);
                    StatsService.scrobbleFinish(player.playlist.track);
                    player.doPlayNext();
                }
            },
            error: function () {
                player.doStop();
            },
            timeupdate: function (e) {
                $rootScope.$applyAsync(function () {
                    player.playlist.position.position = e.jPlayer.status.currentTime;
                });
            },
            swfPath: "/public/js/application/libs/jplayer/",
            supplied: "mp3",
            solution: "html"
        });

        jFrame.bind($.jPlayer.event.canplay, function(){
            $timeout.cancel(timeout);
            $rootScope.$applyAsync(player.isBuffering = false)
        });

        jFrame.bind($.jPlayer.event.waiting, function(){
            timeout = $timeout(function () {
                player.isBuffering = true
            }, 500);
        });

        player.doVolume(Math.min(1, parseFloat($cookies.get("volume")) || 1));

        $rootScope.player = player;

        MonitorSongs($rootScope.player.playlist.tracks, $rootScope);

        $rootScope.$on("songs.deleted", function (e, data) {
            if (player.isLoaded) {
                for (var j = 0; j < data.length; j += 1) {
                    if (player.playlist.track.id === data[j].id) {
                        $rootScope.player.doStop();
                    }
                }
            }
        });

    }

]);