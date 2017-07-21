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
  "Resolved", "$scope", "$location", "SyncKeeper",
  function (Resolved, $scope, $location, SyncKeeper) {

    var syncKeeper = SyncKeeper($scope);

    $scope.tracks = Resolved;
    $scope.tracks_selected = [];
    $scope.album = {};
    $scope.tracks_selected = [];
    $scope.fetch = null;

    $scope.readAlbum = function () {

      if ($scope.tracks.length == 0) {
        $location.url("/");
        return;
      }

      $scope.album = {
        album_title:    aggregateAlbumTitle($scope.tracks),
        album_url:      $scope.tracks.map(field("album_url")).reduce(or, ""),
        album_artist:   $scope.tracks.map(field("album_artist")).reduce(or, ""),
        cover_id:       $scope.tracks.map(field("middle_cover_id")).reduce(or, null),
        artist_url:     $scope.tracks.map(field("artist_url")).reduce(or),
        album_year:     groupYears($scope.tracks),
        album_genre:    groupGenres($scope.tracks),
        length:         aggregateDuration($scope.tracks),
        discs_count:    $scope.tracks.map(field("disk_number")).distinct().length,
        is_various:     $scope.tracks.any(function (t) {
          return t.track_artist !== t.album_artist
        })
      };

    };

    syncKeeper  .songs($scope.tracks)
      .songs($scope.tracks_selected);

    $scope.$watch("tracks", $scope.readAlbum, true);


  }
];
