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

// todo: This must be a service
export default [
  '$rootScope',
  'TrackService',
  'ModalWindow',
  ($rootScope, TrackService, ModalWindow) => {
    $rootScope.action = {
      deleteSongs: async (tracks) => {
        if (!tracks || tracks.length === 0) {
          return
        }
        if (confirm('Are you sure want to delete selected songs?')) {
          const track_ids = tracks.map((t) => t.id)
          await TrackService.unlink({ song_id: track_ids.join(',') })
          $rootScope.$broadcast('songs.deleted', track_ids)
        }
      },
      deleteByArtist: async (track_artist) => {
        if (confirm(`Are you sure want to delete all tracks by ${track_artist}?`)) {
          const deleted_track_ids = await TrackService.deleteByArtist({
            track_artist,
          }).then((response) => response.data.map((t) => t.id))
          $rootScope.$broadcast('songs.deleted', deleted_track_ids)
        }
      },
      editSongs: (tracks) => {
        if (!tracks || tracks.length === 0) {
          return
        }

        ModalWindow({
          template: `${templatePath}/metadata-view.html`,
          controller: 'MetadataController',
          data: {
            songs: tracks,
          },
          closeOnEscape: true,
          closeOnClick: true,
        })
      },
    }
  },
]
