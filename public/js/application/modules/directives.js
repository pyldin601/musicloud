/**
 * Created by Roman on 28.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.directive("peakData", ["$rootScope", "TrackService", "$window", function ($rootScope, TrackService, $window) {
    return {
        restrict: "A",
        link: function (scope, element, attrs) {
            var peaksData = [],
                w = angular.element($window),
                canvas = element[0],
                ctx = canvas.getContext("2d"),

                clearPeaks = function () {
                    peaksData = [];
                    drawCanvas();
                    scope.loading = false;
                },
                loadPeaks = function (data) {
                    peaksData = data;
                    drawCanvas();
                    scope.loading = false;
                },
                drawCanvas = function () {
                    var gradientBase, peak, pos, rate, leftRange, rightRange;

                    canvas.width = element.width();
                    canvas.height = element.height();

                    gradientBase = canvas.height * .75;
                    rate = (peaksData.length / canvas.width) * 3;

                    ctx.fillStyle = "#223344";
                    ctx.globalCompositeOperation = "xor";
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fill();
                    ctx.beginPath();
                    for (var n = 0; n <= canvas.width; n += 1) {
                        if (n % 3 == 2)
                            continue;

                        pos = parseInt(peaksData.length / canvas.width * (n - n % 3));
                        leftRange = Math.max(0, pos - (rate / 2));
                        rightRange = Math.min(peaksData.length - 1, pos + (rate / 2));
                        peak = 1 / 128 * peaksData.slice(leftRange, rightRange).avg();

                        ctx.moveTo(n + .5, parseInt(gradientBase - (gradientBase * peak)) - 1);
                        ctx.lineTo(n + .5, parseInt(gradientBase + ((canvas.height - gradientBase) * peak)) + 1);
                    }
                    ctx.strokeStyle = "#000000";
                    ctx.stroke();
                };

            var watcher = $rootScope.$watch("player.playlist.track", function (changed) {
                scope.loading = true;
                if (!changed) {
                    clearPeaks();
                } else {
                    TrackService.getPeaks(changed.id).success(loadPeaks).error(clearPeaks);
                }
            });

            scope.$on("$destroy", function () {
                watcher();
                w.unbind("resize", drawCanvas);
            });

            scope.loading = false;

            w.bind("resize", drawCanvas);

        }
    };
}]);

MusicLoud.directive("changeArtwork", ["TrackService", "SyncService", function (TrackService, SyncService) {
    return {
        scope: {
            tracks: "=changeArtwork"
        },
        restrict: "A",
        link: function (scope, elem, attrs) {
            var onClickEvent = function (event) {
                var selector = $("<input>");
                selector.attr("type", "file");
                selector.attr("accept", "image/jpeg,image/mjpeg,image/png,image/gif");
                selector.attr("name", "artwork_file");
                selector.on("change", function () {
                    if (this.files.length == 0) return;
                    var that = this.files[0];
                    var track_id = scope.tracks.map(field("id")).join(",");
                    console.log(that);
                    var form = new FormData();
                    form.append("artwork_file", that);
                    form.append("track_id", track_id);
                    TrackService.changeArtwork(form).success(function (data) {
                        SyncService.tracks(data);
                    });
                });
                selector.click();
            };
            elem.bind("click", onClickEvent);
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

MusicLoud.directive("ngVisible", [function () {
    return {
        scope: {
            ngVisible: "="
        },
        restrict: "A",
        link: function (scope, element, attrs) {
            var valueChanged = function (value) {
                element.css("visibility", value ? "visible" : "hidden")
            };
            scope.$watch("ngVisible", valueChanged);
            valueChanged(scope.ngVisible);
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

MusicLoud.directive("playbackProgress", ["$rootScope", function ($rootScope) {
    return {
        template: '<div class="progress-line"></div>' +
        '<div ng-show="player.isLoaded" class="progress-position"></div>' +
        '<div ng-show="player.isLoaded" class="progress-bulb"></div>',
        link: function (scope, elem, attrs) {
            var bulb = elem.find(".progress-bulb"),
                line = elem.find(".progress-position"),
                watcher = $rootScope.$watchCollection("player.playlist.position", function (pos) {

                    var percent;

                    if (!(pos && pos.duration > 0)) return;

                    percent = 100 / pos.duration * pos.position;

                    bulb.css("left", "" + percent + "%");
                    line.css("width", "" + percent + "%");

                });
            elem.on("mousedown", function (event) {
                var offset = elem.offset(),
                    width = elem.width();
                $rootScope.$applyAsync(function () {
                    $rootScope.player.doSeek(100 / width * (event.clientX - offset.left))
                });
            });
            scope.$on("$destroy", watcher);
        }
    };
}]);

//MusicLoud.directive("mlContextMenu", ["$parse", function ($parse) {
//    return {
//        restrict: "A",
//        link: function (scope, elem, attrs) {
//            var data = $parse(attrs.mlContextMenu)(scope);
//            context.attach(elem, data);
//        }
//    }
//}]);

MusicLoud.directive("multiselectList", [function () {
    return {
        scope: {
            multiselectList: "@",
            multiselectDestination: "="
        },
        link: function (scope, elem, attrs) {

            var countSelected = function () {

                var all = elem.find("." + scope.multiselectList + "[multiselect-item]");

                array_copy(all.map(function () {
                    var el = angular.element(this);
                    return el.scope()[el.attr("multiselect-item")]
                }).toArray(), scope.multiselectDestination);

            },
                lastSelected = null;

            elem.on("mousedown selectstart", function (event) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            });

            elem.on("click", function (event) {
                scope.$applyAsync(function () {
                    var all = elem.find("[multiselect-item]");
                    var selected = angular.element(event.target).parents("[multiselect-item]");
                    if (!(event.ctrlKey || event.metaKey)) {
                        all.toggleClass(scope.multiselectList, false);
                    }
                    if (selected.length > 0) {
                        if (event.shiftKey && lastSelected) {
                            ((lastSelected.index() < selected.index())
                                ? lastSelected.nextUntil(selected)
                                : selected.nextUntil(lastSelected)
                            )   .add(selected)
                                .add(lastSelected)
                                .toggleClass(scope.multiselectList, true)
                        } else {
                            selected.toggleClass(scope.multiselectList, true);
                            lastSelected = selected;
                        }
                    } else {
                        lastSelected = null;
                    }
                    countSelected();
                });
            });

        }
    }
}]);

MusicLoud.directive("activeTab", ["$location", "$route", function ($location, $route) {
    return {
        scope: {
            activeTab: "@"
        },
        link: function ($scope, $element, $attributes) {

            var CLASS = "active";

            $element.toggleClass(CLASS, $location.url().match($scope.activeTab) !== null);

            $scope.$on("$routeChangeSuccess", function () {
                if ($route.current.special && $route.current.special.section) {
                    $element.toggleClass(CLASS, $route.current.special.section == $scope.activeTab);
                } else {
                    $element.toggleClass(CLASS, false);
                }
            });

        }
    };
}]);

MusicLoud.directive("trackRating", ["StatsService", function (StatsService) {
    return {
        scope: {
            track: "=trackRating"
        },
        template: '<ul ng-show="track" class="rating-body" ng-class="{shaded: track.track_rating === null}">\
        <li class="rating-star fa" ng-class="{ \'fa-star\': track.track_rating >= n, \'fa-star-o\': track.track_rating < n }" ng-click="rate(n)" ng-repeat="n in [5,4,3,2,1]"></li><li class="rating-remove" ng-click="unrate()">&nbsp;</li>\
        </ul>',
        link: function(scope, elem, attr) {
            elem.on("click", function (event) {
                return false;
            });
            scope.rate = function (value) {
                StatsService.rateTrack(scope.track, value);
            };
            scope.unrate = function () {
                StatsService.unrateTrack(scope.track);
            };
        }
    }
}]);

MusicLoud.directive("progressBar", [function () {
    return {
        scope: {
            progressBar: "="
        },
        restrict: "A",
        link: function (scope, element, attributes) {
            scope.$watch("progressBar", function (value) {
                element.css("width", "" + value + "%");
            });
        }
    }
}]);