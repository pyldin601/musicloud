/**
 * Created by Roman on 28.07.2015.
 */

(function () {

    var MusicLoud = angular.module("MusicLoud");

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

                elem.on("selectstart", function (event) {
                    event.stopPropagation();
                    event.preventDefault();
                });

                elem.on("click touch", function (event) {
                    select(event, false);
                });

                function select(event, force) {
                    scope.$applyAsync(function () {
                        var all = elem.find("[multiselect-item]"),
                            selected = angular.element(event.target).parents("[multiselect-item]"),
                            left, right;

                        if (!(event.ctrlKey || event.metaKey || force)) {
                            all.toggleClass(scope.multiselectList, false);
                        }
                        if (selected.length == 1) {
                            if (event.shiftKey && lastSelected) {
                                left = all.index(lastSelected);
                                right = all.index(selected);

                                ((left < right) ? all.slice(left, right) : all.slice(right, left))
                                    .add(selected)
                                    .add(lastSelected)
                                    .toggleClass(scope.multiselectList, true)
                            } else {
                                selected.toggleClass(scope.multiselectList);
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

    MusicLoud.directive("clickOutside", ["$document", "$parse", "$rootScope", function ($document, $parse, $rootScope) {
        return {
            restrict: "A",
            compile: function ($element, attributes) {

                var fn = $parse(attributes["clickOutside"], null, true);

                return function (scope, element) {

                    var callback = function () {
                            fn(scope, {$event: event})
                        },
                        bindingFunction = function (event) {

                            if (element.find(event.target).length == 0) {
                                if ($rootScope.$$phase) {
                                    scope.$evalAsync(callback);
                                } else {
                                    scope.$apply(callback);
                                }
                            }

                        };

                    $document.bind("click", bindingFunction);

                    scope.$on("$destroy", function () {
                        $document.unbind("click", bindingFunction);
                    });

                };


            }
        }
    }]);

    MusicLoud.directive("mlEnter", ["$document", "$parse", "$rootScope",
        function ($document, $parse, $rootScope) {
            return {
                restrict: "A",
                compile: function ($element, attributes) {

                    var fn = $parse(attributes["mlEnter"], null, true);

                    return function (scope, element) {

                        var callback = function () {
                                fn(scope, {$event: event})
                            },
                            bindingFunction = function (event) {

                                if (event.which == 13) {

                                    if ($rootScope.$$phase) {
                                        scope.$evalAsync(callback);
                                    } else {
                                        scope.$apply(callback);
                                    }

                                }

                            };

                        $document.bind("keypress", bindingFunction);

                        scope.$on("$destroy", function () {
                            $document.unbind("keypress", bindingFunction);
                        });

                    }
                }
            }
        }
    ]);


})();
