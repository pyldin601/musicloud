/*
 * Copyright (c) 2017 Roman Lakhtadyr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

import { aggregateAlbum } from "../util/aggregators";

export default [
  "$routeProvider", "$locationProvider",
  ($routeProvider, $locationProvider) => {
    $locationProvider.html5Mode(true);
    $locationProvider.baseHref = "/library/";

    $routeProvider.when("/", {
      redirectTo: '/artists/'
    });

    $routeProvider.when("/artists/", {
      templateUrl: templatePath + "/artists-view.html",
      controller: "AllArtistsViewController",
      resolve: {
        Resolved: ["SearchService", "$location", (SearchService, $location) => {
          const q = $location.search().q;
          return SearchService.artists({ q: q }, 0).catch(() => {
            $location.url("/");
          });
        }]
      },
      title: "Artists",
      special: {
        section: "artists"
      }
    });

    $routeProvider.when("/artist/:artist", {
      templateUrl: templatePath + "/single-artist-view.html",
      controller: "ArtistViewController",
      resolve: {
        Header: ["$route", "HeadersService", ($route, HeadersService) => {
          const artist = decodeUriPlus($route.current.params.artist);
          return HeadersService.artist(artist).then(response => response.data);
        }],
        Resolved: ["SearchService", "$location", "$route", "$filter",
          (SearchService, $location, $route, $filter) => {
            const artist = decodeUriPlus($route.current.params.artist);
            $route.current.title = $filter("normalizeArtist")(artist);
            return SearchService.tracks({ artist: artist }, 0).catch(() => {
              $location.url("/");
            });
          }
        ]
      },
      title: "Contents by Album Artist",
      special: {
        section: "artist"
      }
    });

    $routeProvider.when("/artist/:artist/:album", {
      controller: "AlbumViewController",
      templateUrl: templatePath + "/album-view.html",
      resolve: {
        albumData: ["SearchService", "$location", "$route", "$filter",
          (SearchService, $location, $route, $filter) => {
            const artist = decodeUriPlus($route.current.params.artist);
            const album = decodeUriPlus($route.current.params.album);

            $route.current.title = String.prototype.concat(
              $filter("normalizeAlbum")(album) + " by " +
              $filter("normalizeArtist")(artist)
            );

            return SearchService.tracks({ artist: artist, album: album, limit: -1 }, 0).catch(() => {
              $location.url("/");
            }).then(aggregateAlbum);
          }
        ]
      },
      title: "Album",
      special: {
        section: "albums"
      }
    });

    $routeProvider.when("/playlist/:playlist", {
      controller: "PlaylistController",
      templateUrl: templatePath + "/tracks-view.html",
      resolve: {
        Playlist: ["PlaylistService", "$route", "$location", (PlaylistService, $route, $location) => {
          const playlist = decodeUriPlus($route.current.params.playlist);
          const promise = PlaylistService.get(playlist);

          promise.then(response => {
            $route.current.title = 'Playlist "' + response.data.name + '"'
          }, () => {
            $location.url("/");
          });
        }],
        Resolved: ["PlaylistService", "$route", function (PlaylistService, $route) {
          const playlist = decodeUriPlus($route.current.params.playlist);
          $route.current.special.section = "playlist/" + playlist;
          return PlaylistService.tracks(playlist);
        }]
      },
      title: "Playlist",
      special: {
      }
    });

    $routeProvider.when("/albums/", {
      templateUrl: templatePath + "/albums-view.html",
      controller: "AllAlbumsViewController",
      resolve: {
        Resolved: ["SearchService", "$location", function (SearchService, $location) {
          const q = $location.search().q;
          return SearchService.albums({ q: q, compilations: 0 }, 0).catch(() => {
            $location.url("/");
          });
        }]
      },
      title: "Albums",
      special: {
        section: "albums"
      }
    });

    $routeProvider.when("/compilations/", {
      templateUrl: templatePath + "/albums-view.html",
      controller: "AllCompilationsViewController",
      resolve: {
        Resolved: ["SearchService", "$location", (SearchService, $location) => {
          const q = $location.search().q;
          return SearchService.albums({ q: q, compilations: 1 }, 0).catch(() => {
            $location.url("/");
          });
        }]
      },
      title: "Compilations",
      special: {
        section: "compilations"
      }
    });

    $routeProvider.when("/genres/", {
      templateUrl: templatePath + "/genres-view.html",
      controller: "AllGenresViewController",
      resolve: {
        Resolved: ["SearchService", "$location", (SearchService, $location) => {
          const q = $location.search().q;
          return SearchService.genres({ q: q }, 0).catch(() => {
            $location.url("/");
          });
        }]
      },
      title: "Genres",
      special: {
        section: "genres"
      }
    });

    $routeProvider.when("/genre/:genre", {
      templateUrl: templatePath + "/single-genre-view.html",
      controller: "GenreViewController",
      resolve: {
        Header: ["$route", "HeadersService", function ($route, HeadersService) {
          const genre = decodeUriPlus($route.current.params.genre);
          return HeadersService.genre(genre).then(response => response.data);
        }],
        Resolved: ["SearchService", "$location", "$route", "$filter",
          function (SearchService, $location, $route, $filter) {
            const genre = decodeUriPlus($route.current.params.genre);

            $route.current.title = $filter("normalizeGenre")(genre);

            return SearchService.tracks({ genre: genre }, 0).catch(() => {
              $location.url("/");
            });
          }
        ]
      },
      title: "Track Genres",
      special: {
        section: "genres"
      }
    });



    $routeProvider.when("/tracks", {
      controller: "TracksViewController",
      templateUrl: templatePath + "/tracks-view.html",
      resolve: {
        Resolved: ["SearchService", "$location",
          (SearchService, $location) => {
            const q = $location.search().q;
            const s = $location.search().s;

            return SearchService.tracks({ q: q, s: s }, 0).catch(() => {
              $location.url("/");
            });
          }
        ]
      },
      title: "Tracks",
      special: {
        section: "tracks"
      }
    });

    $routeProvider.otherwise({
      redirectTo: "/"
    });
  },
];
