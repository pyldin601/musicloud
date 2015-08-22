(function () {

    var MusicLoud = angular.module("MusicLoud");

    MusicLoud.run(["$rootScope", "StatsService", "SyncService", "$cookies", "$timeout", "SyncKeeper",

        function ($rootScope, StatsService, SyncService, $cookies, $timeout, SyncKeeper) {

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

                        player.playlist.fetch(player.playlist.tracks.length).then(function (tracks) {
                            if (tracks.length > 0) {
                                player.playlist.tracks = player.playlist.tracks.concat(tracks);
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

                            if (player.playlist.fetch && (index + 1 == player.playlist.tracks.length - 1)) {
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

            SyncKeeper($rootScope).songs($rootScope.player.playlist.tracks);

            $rootScope.$on("songs.deleted", function (e, data) {
                if (player.isLoaded) {
                    for (var j = 0; j < data.length; j += 1) {
                        if (player.playlist.track.id === data[j]) {
                            $rootScope.player.doStop();
                        }
                    }
                }
            });

        }

    ]);

    MusicLoud.factory("StatsService", ["$http", "$filter", function ($http, $filter) {
        return {
            incrementPlays: function (track) {
                return $http.post("/api/stats/played", {id: track.id}).success(function () {
                    track.times_played += 1;
                    track.last_played_date = new Date().getTime() / 1000;
                });
            },
            incrementSkips: function (track) {
                return $http.post("/api/stats/skipped", {id: track.id}).success(function () {
                    track.times_skipped += 1;
                });
            },
            rateTrack: function (track, rating) {
                track.track_rating = rating;
                return $http.post("/api/stats/rate", {id: track.id, rating: rating});
            },
            unrateTrack: function (track) {
                track.track_rating = null;
                return $http.post("/api/stats/unrate", {id: track.id});
            },
            scrobbleStart: function (track) {
                return $http.post("/api/scrobbler/nowPlaying", {id: track.id});
            },
            scrobbleFinish: function (track) {
                return $http.post("/api/scrobbler/scrobble", {id: track.id});
            }
        }
    }]);

    MusicLoud.directive("seekController", ["$rootScope", function ($rootScope) {
        return {
            link: function (scope, element, attrs) {
                element.on("mousedown", function (event) {
                    var offset = element.offset(),
                        width = element.width();
                    $rootScope.$applyAsync(function () {
                        $rootScope.player.doSeek(100 / width * (event.clientX - offset.left))
                    });
                });
            }
        }
    }]);


    MusicLoud.directive("trackPosition", ["$rootScope", function ($rootScope) {
        return {
            link: function (scope, element, attrs) {
                var watcher = $rootScope.$watchCollection("player.playlist.position", function (pos) {
                    element.css("width", "" + (100 / pos.duration * pos.position) + "%")
                });
                scope.$on("$destroy", watcher);
            }
        }
    }]);

    MusicLoud.directive("volumeController", ["$rootScope", "$document", function ($rootScope, $document) {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {
                var line = element.find(".value-line"),
                    bulb = element.find(".volume-bulb"),
                    doc = angular.element($document),
                    unbind = $rootScope.$watch("player.volume", function (value) {
                        line.css("height", "" + parseInt(100 * value) + "%");
                        bulb.css("bottom", "" + parseInt(100 * value) + "%");
                    }),
                    dragEvents = {
                        mousePos: 0,
                        mouseOffset: 0,
                        dragStart: function (event) {
                            dragEvents.mouseOffset = element.height() - (event.clientY + $(window).scrollTop() - element.offset().top);
                            dragEvents.mousePos    = event.clientY;
                            doc .bind("mousemove",     mouseEvents.mousemove)
                                .bind("mouseup",       mouseEvents.mouseup);
                            $(".ctrl-volume").addClass("drag");
                        },
                        drag: function (event) {
                            var delta = dragEvents.mousePos - event.clientY,
                                value = Math.min(element.height(), Math.max(0, dragEvents.mouseOffset + delta)),
                                vol = 1 / element.height() * value;
                            $rootScope.$applyAsync($rootScope.player.doVolume(vol));
                        },
                        dragStop: function () {
                            doc .unbind("mousemove",   mouseEvents.mousemove)
                                .unbind("mouseup",     mouseEvents.mouseup);
                            $(".ctrl-volume").removeClass("drag");
                        }
                    },
                    mouseEvents = {
                        mousedown: function (event) {
                            dragEvents.dragStart(event);
                            dragEvents.drag(event);
                            event.stopPropagation();
                            event.preventDefault();
                        },
                        mousemove: function (event) {
                            dragEvents.drag(event);
                            event.stopPropagation();
                            event.preventDefault();
                        },
                        mouseup: function (event) {
                            dragEvents.dragStop(event);
                            event.stopPropagation();
                            event.preventDefault();
                        }
                    };

                element.bind("mousedown", mouseEvents.mousedown);

                scope.$on("$destroy", function () {
                    unbind();
                })
            }
        }
    }]);


    MusicLoud.directive("actionPlay", ["$rootScope", function ($rootScope) {
        return {
            scope: {
                actionPlay: "=",
                actionContext: "=",
                actionResolver: "="
            },
            restrict: "A",
            link: function (scope, elem, attrs) {
                elem.on("dblclick", function () {
                    $rootScope.player.doPlay(
                        scope.actionPlay,
                        scope.actionContext,
                        scope.actionResolver
                    );
                });
            }
        }
    }]);

    MusicLoud.directive("play", ["$rootScope", function ($rootScope) {
        return {
            scope: {
                play: "="
            },
            restrict: "A",
            link: function (scope, elem, attrs) {
                elem.on("click", function () {
                    $rootScope.player.doPlay(scope.play[0], scope.play[1] || null, scope.play[2] || null);
                });
            }
        }
    }]);


})();

