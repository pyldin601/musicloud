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

            };

            elem.on("mousedown", function (event) {

                event.preventDefault();

                return false;

            });

            elem.on("click", function (event) {
                scope.$applyAsync(function () {
                    var all = elem.find("[multiselect-item]");
                    var selected = angular.element(event.target).parents("[multiselect-item]");
                    all.toggleClass(scope.multiselectList, false);
                    if (selected.length > 0) {
                        selected.toggleClass(scope.multiselectList);
                    }
                    countSelected();
                });
            });

        }
    }
}]);