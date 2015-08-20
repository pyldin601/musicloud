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

            elem.on("selectstart contextmenu", function (event) {
                event.preventDefault();
                event.stopPropagation();
            });

            elem.on("click", function (event) {
                select(event);
            });

            function select(event) {
                scope.$applyAsync(function () {
                    var all = elem.find("[multiselect-item]");
                    var selected = angular.element(event.target).parents("[multiselect-item]");

                    if (!(event.ctrlKey || event.metaKey)) {
                        all.toggleClass(scope.multiselectList, false);
                    }
                    if (selected.length == 1) {
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
            }

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
