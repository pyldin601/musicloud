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

export default [() => scope => {
  const keeper = {
    songs: songs => {
      scope.$on('songs.deleted', (e, payload) => {
        scope.$applyAsync(() => {
          for (const id in payload) {
            for (let i = songs.length - 1; i >= 0; i -= 1) {
              if (songs[i].id === id) {
                songs.splice(i, 1);
                break;
              }
            }
          }
        });
      });
      return keeper;
    },
    playlistSongs: function (songs) {
      scope.$on('playlist.songs.deleted', function (e, payload) {
        scope.$applyAsync(function () {
          for (const id in payload) {
            for (let i = songs.length - 1; i >= 0; i -= 1) {
              if (songs[i].link_id === id) {
                songs.splice(i, 1);
                break;
              }
            }
          }
        });
      });
      return keeper;
    },
    groups: gs => {
      scope.$on('songs.deleted', (e, payload) => {
        scope.$applyAsync(function () {
          gs.removeItems('id', payload);
        });
      });
      return keeper;
    }
  };
  return keeper;
}];
