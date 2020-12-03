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

import { first } from 'lodash'

export default [
  'TrackService',
  'SyncService',
  (TrackService, SyncService) => {
    return {
      scope: {
        tracks: '=changeArtwork',
      },
      restrict: 'A',
      link: function (scope, elem, attrs) {
        const onClickEvent = () => {
          const selector = $('<input>')
          selector.attr('type', 'file')
          selector.attr('accept', 'image/jpeg,image/mjpeg,image/png,image/gif')
          selector.attr('name', 'artwork_file')
          selector.on('change', function () {
            if (this.files.length === 0) {
              return
            }

            const that = first(this.files)
            const track_id = scope.tracks.map((t) => t.id).join(',')

            TrackService.changeArtwork({ artworkFile: that, trackId: track_id }).then(function (
              tracks,
            ) {
              SyncService.tracks(tracks)
            })
          })
          selector.click()
        }
        elem.bind('click', onClickEvent)
      },
    }
  },
]
