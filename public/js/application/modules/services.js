/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");






MusicLoud.service("ModalWindow", ["$templateRequest", "$controller", "$rootScope", "$compile", "$document",
    function ($templateRequest, $controller, $rootScope, $compile, $document) {
        var defaults = {
                controller: null,
                closeOnEscape: true,
                closeOnClick: true,
                data: {},
                scope: null
            },

            $an = angular;

        return function (opts) {

            var options = $an.copy(defaults);

            $an.extend(options, opts);

            $templateRequest(options.template).then(function (template) {

                var newScope = $an.isObject(options.scope) ? options.scope.$new() : $rootScope.$new(),
                    body = $an.element("body"),
                    $modal = $an.element(template),

                    onEscapeEvent = function (event) {
                        if (event.which == 27) {
                            newScope.closeThisWindow()
                        }
                    },

                    onMouseClickEvent = function (event) {
                        if ($an.element(event.target).parents($modal).length == 0) {
                            newScope.closeThisWindow()
                        }
                    },

                    compile = function () {
                        $compile($modal.contents())(newScope);
                    };



                newScope.closeThisWindow = function () {
                    $modal.remove();
                    newScope.$destroy();
                };

                newScope.$on("$destroy", function () {
                    body.off("keyup", onEscapeEvent);
                    body.off("click", onMouseClickEvent);
                });

                for (var k in options.data) if (options.data.hasOwnProperty(k)) {
                    newScope[k] = options.data[k]
                }

                if (options.closeOnEscape) {
                    body.bind("keyup", onEscapeEvent);
                }

                if (options.closeOnClick) {
                    body.bind("click", onMouseClickEvent);
                }

                $modal.appendTo(body);

                if (options.controller) {
                    var controllerInstance = $controller(options.controller, {
                        $scope: newScope,
                        $element: $modal
                    });
                    $modal.data('$modalWindowController', controllerInstance);
                }

                if (newScope.$$phase) {
                    newScope.$applyAsync(compile)
                } else {
                    newScope.$apply(compile);
                }

            });
        };
    }
]);