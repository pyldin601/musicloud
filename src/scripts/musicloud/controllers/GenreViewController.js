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
  'Resolved',
  'Header',
  'SearchService',
  '$scope',
  '$routeParams',
  'GroupingService',
  'SyncKeeper',
  function (Resolved, Header, SearchService, $scope, $routeParams, GroupingService, SyncKeeper) {
    var genre = decodeUriPlus($routeParams.genre),
      gs = GroupingService('track_album'),
      syncKeeper = SyncKeeper($scope)

    $scope.header = Header
    $scope.tracks = Resolved
    $scope.tracks_selected = []
    $scope.albums = gs.getGroups()
    $scope.busy = false
    $scope.end = false

    $scope.fetch = SearchService.tracks.curry({ genre: genre })

    gs.addItems(Resolved)

    $scope.load = function () {
      $scope.busy = true
      $scope.fetch($scope.tracks.length).then(function (data) {
        if (data.length > 0) {
          array_add(data, $scope.tracks)
          gs.addItems(data)
          $scope.busy = false
        } else {
          $scope.end = true
        }
      })
    }

    syncKeeper.songs($scope.tracks).songs($scope.tracks_selected).groups(gs)
  },
]
