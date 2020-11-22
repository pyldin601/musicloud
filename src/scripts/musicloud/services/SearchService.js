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

export default [
  '$http',
  'SyncService',
  ($http, SyncService) => ({
    artists: function (opts, offset, special) {
      var uri = {}

      uri.o = offset || 0

      if (opts.q) {
        uri.q = opts.q
      }

      uri.l = opts.limit || 50

      return $http
        .get('/api/catalog/artists?' + serialize_uri(uri), special)
        .then(function (response) {
          return deflateCollection(response.data)
        })
    },
    albums: function (opts, offset, special) {
      var uri = {}

      uri.o = offset || 0

      if (opts.q !== undefined) {
        uri.q = opts.q
      }
      if (opts.compilations !== undefined) {
        uri.compilations = opts.compilations
      }

      uri.l = opts.limit || 50

      return $http
        .get('/api/catalog/albums?' + serialize_uri(uri), special)
        .then(function (response) {
          return deflateCollection(response.data)
        })
    },
    genres: function (opts, offset, special) {
      var uri = {}

      uri.o = offset || 0

      if (opts.q) {
        uri.q = opts.q
      }

      uri.l = opts.limit || 50

      return $http
        .get('/api/catalog/genres?' + serialize_uri(uri), special)
        .then(function (response) {
          return deflateCollection(response.data)
        })
    },
    tracks: function (opts, offset, special) {
      var uri = {}

      uri.o = offset || 0

      if (opts.q !== undefined) {
        uri.q = opts.q
      }
      if (opts.s !== undefined) {
        uri.sort = opts.s
      }
      if (opts.compilations !== undefined) {
        uri.compilations = opts.compilations
      }
      if (opts.artist !== undefined) {
        uri.artist = opts.artist
      }
      if (opts.album !== undefined) {
        uri.album = opts.album
      }
      if (opts.genre !== undefined) {
        uri.genre = opts.genre
      }
      uri.l = opts.limit || 50

      return $http
        .get('/api/catalog/tracks?' + serialize_uri(uri), special)
        .then(function (response) {
          return SyncService.tracks(deflateCollection(response.data))
        })
    },
  }),
]
