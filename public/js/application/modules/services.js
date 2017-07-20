/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");


MusicLoud.factory("PlaylistService", ["$http", function ($http) {
    return {
        get: function (playlistId) {
            return $http.get("/api/playlist/get?" + serialize_uri({ playlist_id: playlistId }));
        },
        list: function () {
            return $http.get("/api/catalog/playlists");
        },
        tracks: function (playlistId) {
            return $http.get("/api/catalog/playlistTracks?" + serialize_uri({ playlist_id: playlistId })).then(function (response) {
                return deflateCollection(response.data);
            });
        },
        addTracks: function (playlist, coll) {
            return $http.post("/api/playlist/addTracks", {
                playlist_id: playlist.id,
                track_id: coll.map(field("id")).join(",")
            });
        },
        removeTracks: function (coll) {
            return $http.post("/api/playlist/removeTracks", {
                link_id: coll.map(field("link_id")).filter(pass).join(",")
            });
        },
        create: function (name) {
            return $http.post("/api/playlist/create", {
                name: name
            });
        },
        remove: function (playlist) {
            return $http.post("/api/playlist/delete", {
                playlist_id: playlist.id
            });
        }
    }
}]);

MusicLoud.factory("SearchService", ["$http", "SyncService", function ($http, SyncService) {
    return {
        artists: function (opts, offset, special) {
            var uri = {};

            uri.o = offset || 0;

            if (opts.q) { uri.q = opts.q }

            uri.l = opts.limit || 50;

            return $http.get("/api/catalog/artists?" + serialize_uri(uri), special).then(function (response) {
                return deflateCollection(response.data);
            });
        },
        albums: function (opts, offset, special) {
            var uri = {};

            uri.o = offset || 0;

            if (opts.q !== undefined) { uri.q = opts.q }
            if (opts.compilations !== undefined) { uri.compilations = opts.compilations }

            uri.l = opts.limit || 50;

            return $http.get("/api/catalog/albums?" + serialize_uri(uri), special).then(function (response) {
                return deflateCollection(response.data);
            });
        },
        genres: function (opts, offset, special) {
            var uri = {};

            uri.o = offset || 0;

            if (opts.q) { uri.q = opts.q }

            uri.l = opts.limit || 50;

            return $http.get("/api/catalog/genres?" + serialize_uri(uri), special).then(function (response) {
                return deflateCollection(response.data);
            });
        },
        tracks: function (opts, offset, special) {

            var uri = {};

            uri.o = offset || 0;

            if (opts.q !== undefined) { uri.q = opts.q }
            if (opts.s !== undefined) { uri.sort = opts.s }
            if (opts.compilations !== undefined) { uri.compilations = opts.compilations }
            if (opts.artist !== undefined) { uri.artist = opts.artist }
            if (opts.album !== undefined) { uri.album = opts.album }
            if (opts.genre !== undefined) { uri.genre = opts.genre }
            uri.l = opts.limit || 50;

            return $http.get("/api/catalog/tracks?" + serialize_uri(uri), special).then(function (response) {
                return SyncService.tracks(deflateCollection(response.data));
            });
        }
    };
}]);

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