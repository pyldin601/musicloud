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
  '$scope',
  'SearchService',
  '$timeout',
  'SyncService',
  '$q',

  function ($scope, SearchService, $timeout, SyncService, $q) {
    var promise,
      delay = 200,
      canceller

    $scope.query = ''
    $scope.results = {}

    $scope.$watch('query', function (newValue) {
      if (!newValue) {
        $scope.reset()
        return
      }
      $timeout.cancel(promise)
      promise = $timeout($scope.search, delay)
    })

    $scope.search = function () {
      if (canceller) canceller.resolve()

      canceller = $q.defer()

      $scope.results.artists_busy = true
      $scope.results.albums_busy = true
      $scope.results.tracks_busy = true

      SearchService.tracks({ q: $scope.query, limit: 15 }, 0, { timeout: canceller.promise }).then(
        function (response) {
          $scope.results.tracks = response
          $scope.results.tracks_busy = false
        },
      )

      SearchService.artists(
        {
          q: $scope.query,
          limit: 15,
        },
        0,
        { timeout: canceller.promise },
      ).then(function (response) {
        $scope.results.artists = response
        $scope.results.artists_busy = false
      })

      SearchService.albums({ q: $scope.query, limit: 15 }, 0, { timeout: canceller.promise }).then(
        function (response) {
          $scope.results.albums = response
          $scope.results.albums_busy = false
        },
      )
    }

    $scope.$on('$routeChangeSuccess', function () {
      $scope.reset()
    })

    $scope.reset = function () {
      $scope.query = ''
      $scope.results = {
        artists: [],
        albums: [],
        tracks: [],
        artists_busy: false,
        albums_busy: false,
        tracks_busy: false,
      }
    }
  },
]
