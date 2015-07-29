/**
 * Created by Roman on 28.07.2015.
 */

var homecloud = angular.module("HomeCloud");


homecloud.directive("actionPlay", ["$rootScope", function ($rootScope) {
    return {
        scope: {
            actionPlay: "=",
            actionContext: "="
        },
        restrict: "A",
        link: function (scope, elem, attrs) {
            elem.on("dblclick", function () {
                $rootScope.player.doPlay(scope.actionPlay, scope.actionContext);
            });
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
                $rootScope.player.doSeek(100 / width * (event.clientX - offset.left));
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