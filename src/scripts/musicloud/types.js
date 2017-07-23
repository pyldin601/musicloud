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
export type Track = {|
  id: string,
  file_id: string,
  file_name: string,
  bitrate: number,
  length: number,
  track_title: string,
  track_artist: string,
  track_album: string,
  track_genre: string,
  track_number: ?number,
  track_comment: string,
  track_year: string,
  track_rating: ?number,
  is_favourite: boolean,
  is_compilation: boolean,
  disc_number: ?number,
  album_artist: string,
  times_played: number,
  times_skipped: number,
  last_played_date: number,
  created_date: number,
  small_cover_id: string,
  middle_cover_id: string,
  big_cover_id: string,
  format: string,
  artist_url: string,
  album_url: string,
  genre_url: string,
|};

export type Playlist = {|
  id: string,
  name: string,
  playlist_url: string,
|};

export type PlaylistTrack = Track & {| link_id: string |};

export type Album = {|
  album_title: string,
  album_url: string,
  album_artist: string,
  cover_id: ?string,
  artist_url: string,
  album_year: string,
  album_genre: string,
  length: number,
  discs_count: number,
  is_various: boolean,
|};

export type TrackColumns = $Keys<Track>;

export type PlaylistTrackColumns = $Keys<PlaylistTrack>;

