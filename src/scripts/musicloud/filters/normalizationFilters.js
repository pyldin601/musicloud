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
import zeroFill from 'zero-fill';
import type { Track } from '../types';

const DEFAULT_TRACK_TITLE = 'Unknown Title';
const DEFAULT_ALBUM = 'Unknown Album';
const DEFAULT_ARTIST = 'Unknown Artist';
const DEFAULT_GENRE = 'Unknown Genre';

export const normalizeTime = [() => (time: number): string => {
  const integerTime = Math.floor(time);
  const hours = Math.floor(integerTime / 3600);
  const minutes = Math.floor(integerTime / 60) % 60;
  const seconds = integerTime % 60;

  let result = [];

  if (hours > 0) {
    result.push(zeroFill(2, hours));
  }

  result.push(zeroFill(2, minutes));
  result.push(zeroFill(2, seconds));

  return result.join(':');
}];

export const normalizeTrackTitle = [() =>
  (track: ?Track) => track ? track.track_title || track.file_name || DEFAULT_TRACK_TITLE : null
];

export const normalizeTrackNumber = [() =>
  (track: ?Track) => {
    if (!track || !track.track_number) {
      return null;
    }
    if (track.disc_number) {
      return `${track.disc_number}.${zeroFill(2, track.track_number)}`;
    }
    return `${zeroFill(2, track.track_number)}`;
  }
];

export const normalizeAlbum = [() => (album: string) => album || DEFAULT_ALBUM];

export const normalizeArtist = [() => (artist: string) => artist || DEFAULT_ARTIST];

export const normalizeGenre = [() => (genre: string) => genre || DEFAULT_GENRE];

export const normalizeBitrate = [() => (bitrate: number) => `${(parseInt(bitrate / 1000 / 8) * 8)} kbps`];
