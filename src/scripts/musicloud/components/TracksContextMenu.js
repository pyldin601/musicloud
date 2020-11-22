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

import { encodeHTML } from 'entities'

const tracksMenu = (selectedTracks, playlists, actions) => {
  if (selectedTracks.length === 0) {
    return null
  }

  const menuData = [
    {
      type: 'item',
      text: `<i class="fa fa-pencil-square item-icon"></i> Edit metadata`,
      action() {
        actions.editSongs(selectedTracks)
      },
    },
    {
      type: 'item',
      text: `<i class="fa fa-minus-square item-icon"></i> Delete track(s) completely`,
      action() {
        actions.deleteSongs(selectedTracks)
      },
    },
  ]

  if (selectedTracks.length > 0) {
    if (selectedTracks[0].link_id) {
      menuData.push({
        type: 'item',
        text: `<i class="fa fa-minus-square item-icon"></i> Delete track(s) from playlist`,
        action: () => {
          actions.removeTracksFromPlaylist(selectedTracks)
        },
      })
    }
  }

  if (selectedTracks.length === 1) {
    menuData.push({ type: 'divider' })
    if (selectedTracks[0].album_artist) {
      menuData.push({
        type: 'item',
        text: `<i class="fa fa-search item-icon"></i> Show all by <b>${encodeHTML(
          selectedTracks[0].album_artist,
        )}</b>`,
        href: selectedTracks[0].artist_url,
      })
    }
    if (selectedTracks[0].track_album) {
      menuData.push({
        type: 'item',
        text: `<i class="fa fa-search item-icon"></i> Show all from <b>${encodeHTML(
          selectedTracks[0].track_album,
        )}</b>`,
        href: selectedTracks[0].album_url,
      })
    }
    if (selectedTracks[0].track_genre) {
      menuData.push({
        type: 'item',
        text: `<i class="fa fa-search item-icon"></i> Show all by genre <b>${encodeHTML(
          selectedTracks[0].track_genre,
        )}</b>`,
        href: selectedTracks[0].genre_url,
      })
    }
  }

  menuData.push({ type: 'divider' })

  menuData.push({
    type: 'sub',
    text: '<i class="fa fa-plus item-icon"></i> Add to playlist',
    data: playlists.map((playlist) => ({
      type: 'item',
      text: `<i class="fa fa-list item-icon"></i> ${encodeHTML(playlist.name)}`,
      action: () => {
        actions.addTracksToPlaylist(playlist, selectedTracks)
      },
    })),
  })

  return menuData
}

export default [
  '$rootScope',
  ($rootScope) => {
    $rootScope.selectedTracksMenu = (selectedTracks) =>
      tracksMenu(selectedTracks, $rootScope.playlists, {
        editSongs: (tracks) => $rootScope.action.editSongs(tracks),
        deleteSongs: (tracks) => $rootScope.action.deleteSongs(tracks),
        removeTracksFromPlaylist: (tracks) =>
          $rootScope.playlistMethods.removeTracksFromPlaylist(tracks),
        addTracksToPlaylist: (playlist, tracks) =>
          $rootScope.playlistMethods.addTracksToPlaylist(playlist, tracks),
      })
  },
]
