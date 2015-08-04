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

homecloud.directive("volumeController", ["$rootScope", function ($rootScope) {
    return {
        restrict: "A",
        link: function (scope, elem, attrs) {
            var line = elem.find(".value-line"),
                unbind = $rootScope.$watch("player.volume", function (value) {
                    line.css("height", "" + parseInt(100 * value) + "%");
                });
            elem.on("mousedown mousemove", function (event) {
                if (event.which == 1) {
                    var offset = elem.offset().top - $(window).scrollTop(),
                        vol = 1 / elem.height() * (elem.height() - (event.clientY - offset));
                    $rootScope.$applyAsync($rootScope.player.doVolume(vol));
                }
                return false;
            });
            scope.$on("$destroy", function () {
                unbind();
            })
        }
    }
}]);

homecloud.directive("playbackProgress", ["$rootScope", function ($rootScope) {
    return {
        template: '<div class="progress-line"></div><div ng-show="player.playlist.position.duration > 0" class="progress-position"></div><div ng-show="player.playlist.position.duration > 0" class="progress-bulb"></div>',
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
            scope.rate = function (value) {
                StatsService.rateTrack(scope.track, value);
            };
            scope.unrate = function () {
                StatsService.unrateTrack(scope.track);
            };
        }
    }
}]);