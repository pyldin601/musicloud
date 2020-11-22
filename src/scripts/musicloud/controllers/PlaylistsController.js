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
  '$rootScope',
  'PlaylistService',
  function ($scope, PlaylistService) {
    $scope.playlists = []
    $scope.playlistMethods = {
      reloadPlaylists: function () {
        PlaylistService.list().then(function (response) {
          array_copy(response.data, $scope.playlists)
        })
      },
      createNewPlaylist: function () {
        var name = prompt('Please enter name for new playlist', 'New playlist')
        if (name !== null && name !== '') {
          PlaylistService.create(name).then(
            function (response) {
              $scope.playlists.push(response.data)
            },
            function (response) {
              alert(response.data.message)
            },
          )
        }
      },
      deletePlaylist: function (playlist) {
        if (confirm('Are you sure want to delete playlist "' + playlist.name + '"')) {
          PlaylistService.remove(playlist)
          $scope.$broadcast('playlist.deleted', playlist)
          $scope.playlists.splice($scope.playlists.indexOf(playlist), 1)
        }
        return false
      },
      addTracksToPlaylist: function (playlist, tracks) {
        PlaylistService.addTracks(playlist, tracks)
      },
      removeTracksFromPlaylist: function (tracks) {
        PlaylistService.removeTracks(tracks)
        $scope.$broadcast('playlist.songs.deleted', tracks.map(field('link_id')))
      },
    }

    $scope.playlistMethods.reloadPlaylists()
  },
]
