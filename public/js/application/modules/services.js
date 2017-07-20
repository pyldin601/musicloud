/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");




MusicLoud.factory("GroupingService", [function () {

    return function (key) {

        var groups = [],

            getGroup = function (k) {
                if (groups.length == 0 || groups[groups.length - 1].key !== k) {
                    groups.push({ key: k, items: [] })
                }
                return groups[groups.length - 1].items;
            };

        console.log("Initialized groups with key " + key);
        return {
            addItems: function (coll) {
                console.log("Adding " + coll.length + " items into groups");
                for (var i = 0, length = coll.length; i < length; i += 1) {
                    getGroup(coll[i][key]).push(coll[i]);
                }
            },
            removeItems: function (itemKey, coll) {
                for (var j = groups.length - 1; j >= 0; j--) {
                    for (var i = groups[j].items.length - 1; i >= 0; i--) {
                        for (var k = coll.length - 1; k >= 0; k--) {
                            if (groups[j].items[i][itemKey] == coll[k]) {
                                console.log("Removing " + coll[k] + " from group " + groups[j].key);
                                groups[j].items.splice(i, 1);
                                break;
                            }
                        }
                    }
                    if (groups[j].items.length == 0) {
                        groups.splice(j, 1);
                    }
                }
            },
            removeGroup: function (group) {
                for (var i = groups.length - 1; i >= 0; i--) {
                    if (groups[i].key === group) {
                        groups.splice(i, 1);
                        break;
                    }
                }
            },
            getGroups: function () {
                console.log("Requested groups collection");
                return groups;
            },
            clear: function () {
                console.log("Cleaning groups");
                while (groups.length) {
                    groups.shift();
                }
            }
        }
    };

}]);


MusicLoud.factory("SyncService", [function () {
    var trackSync  = sync("id");
    var artistSync = sync("id");
    var albumSync  = sync("id");
    return {
        tracks: trackSync,
        track: function (track) {
            return trackSync([track]).shift();
        },

        artists: artistSync,
        artist: function (artist) {
            return artistSync([artist]).shift();
        },

        albums: albumSync,
        album: function (album) {
            return albumSync([album]).shift();
        }
    };
}]);



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