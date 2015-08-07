/**
 * Created by Roman on 28.07.2015.
 */

var homecloud = angular.module("HomeCloud");


homecloud.directive("actionPlay", ["$rootScope", function ($rootScope) {
    return {
        scope: {
            actionPlay: "=",
            actionContext: "=",
            actionResolver: "="
        },
        restrict: "A",
        link: function (scope, elem, attrs) {
            elem.on("dblclick", function () {
                $rootScope.player.doPlay(scope.actionPlay, scope.actionContext, scope.actionResolver);
            });
        }
    }
}]);

homecloud.directive("play", ["$rootScope", function ($rootScope) {
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

homecloud.directive("ngVisible", [function () {
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

homecloud.directive("volumeController", ["$rootScope", function ($rootScope) {
    return {
        restrict: "A",
        link: function (scope, element, attrs) {
            var line = element.find(".value-line"),
                bulb = element.find(".volume-bulb"),
                doc = $(document),
                unbind = $rootScope.$watch("player.volume", function (value) {
                    line.css("height", "" + parseInt(100 * value) + "%");
                    bulb.css("bottom", "" + parseInt(100 * value) + "%");
                }),
                dragEvents = {
                    mousePos: 0,
                    mouseOffset: 0,
                    dragStart: function (event) {
                        dragEvents.mouseOffset = element.height() - (event.clientY - element.offset().top - $(window).scrollTop());
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

homecloud.directive("playbackProgress", ["$rootScope", function ($rootScope) {
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

homecloud.directive("multiselectList", [function () {
    return {
        scope: {
            multiselectList: "@",
            multiselectDestination: "="
        },
        link: function (scope, elem, attrs) {

            var countSelected = function () {

                var all = elem.find("." + scope.multiselectList + "[multiselect-item]");

                scope.multiselectDestination = all.map(function () {

                    var element = angular.element(this);

                    return element.scope()[element.attr("multiselect-item")]

                }).toArray();

            },
                lastSelected = null;

            elem.on("mousedown", function (event) {
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

homecloud.directive("activeTab", ["$location", "$route", function ($location, $route) {
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

homecloud.directive("trackRating", ["StatsService", function (StatsService) {
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