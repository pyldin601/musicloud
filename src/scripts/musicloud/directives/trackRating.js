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

const ratingTemplate = `
    <ul ng-show="track" class="rating-body" ng-class="{shaded: track.track_rating === null}">
        <li class="rating-star fa"
            ng-class="{ 'fa-star': track.track_rating >= n, 'fa-star-o': track.track_rating < n }"
            ng-click="rate(n)"
            ng-repeat="n in [5,4,3,2,1]">
        </li>
        <li class="rating-remove" ng-click="unrate()">&nbsp;</li>
    </ul>
`

export default [
  'StatsService',
  (StatsService) => ({
    scope: {
      track: '=trackRating',
    },
    template: ratingTemplate,
    link: function (scope, elem, attr) {
      elem.on('click', function () {
        return false
      })
      scope.rate = function (value) {
        StatsService.rateTrack(scope.track, value)
      }
      scope.unrate = function () {
        StatsService.unrateTrack(scope.track)
      }
    },
  }),
]
