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

// @flow
import {
  aggregateGenres,
  aggregateAlbumTitle,
  aggregateYears,
  isVariousArtists,
  aggregateDuration,
  aggregateDiscCount,
} from "../util/aggregators";
import type { Track, Album } from "../types";

const or = (a, b) => a || b;

export default [
  "Resolved", "$scope", "$location", "SyncKeeper",
  function (Resolved: Array<Track>, $scope: *, $location: *, SyncKeeper: *) {

    const syncKeeper = SyncKeeper($scope);

    $scope.tracks = Resolved;
    $scope.tracks_selected = ([] : Array<Track>);
    $scope.album = {};
    $scope.tracks_selected = [];
    $scope.fetch = null;

    $scope.readAlbum = function () {

      if ($scope.tracks.length === 0) {
        $location.url("/");
        return;
      }

      $scope.album = {
        album_title:    aggregateAlbumTitle($scope.tracks),
        album_url:      $scope.tracks.map(t => t.album_url).reduce(or, ""),
        album_artist:   $scope.tracks.map(t => t.album_artist).reduce(or, ""),
        cover_id:       $scope.tracks.map(t => t.middle_cover_id).reduce(or, null),
        artist_url:     $scope.tracks.map(t => t.artist_url).reduce(or),
        album_year:     aggregateYears($scope.tracks),
        album_genre:    aggregateGenres($scope.tracks),
        length:         aggregateDuration($scope.tracks),
        discs_count:    aggregateDiscCount($scope.tracks),
        is_various:     isVariousArtists($scope.tracks),
      };

    };

    syncKeeper  .songs($scope.tracks)
      .songs($scope.tracks_selected);

    $scope.$watch("tracks", $scope.readAlbum, true);

  }
];
