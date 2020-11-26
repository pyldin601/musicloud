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

import { first, join, uniq, min, max, sum, isEmpty, isNumber } from 'lodash'
import { or } from './exp'

export const aggregateGenres = (tracks) => {
  const genres = uniq(tracks.map((t) => t.track_genre))
  switch (genres.length) {
    case 0:
      return '-'
    case 1:
      return first(genres)
    case 2:
      return join(genres, ', ')
    case 3:
      return `${genres[0]}, ${genres[1]} and ${genres[2]}`
    default:
      return `${genres[0]}, ${genres[1]} and ${genres.length - 2} others`
  }
}

export const aggregateYears = (tracks) => {
  const isNumeric = (str) => !isNaN(parseInt(str, 10))
  const years = uniq(tracks.map((t) => t.track_year)).filter(isNumeric)
  switch (years.length) {
    case 0:
      return '-'
    case 1:
      return join(years, ', ')
    default:
      return `${min(years)} - ${max(years)}`
  }
}

export const aggregateAlbumTitle = (tracks) =>
  tracks.map((t) => t.track_album).reduce((a, b) => a || b, '')

export const aggregateTrackArtists = (tracks) => {
  const artists = Array.from(new Set(tracks.map((t) => t.track_artist).filter((t) => !isEmpty(t))))
  const prefix = 'Including'

  switch (artists.length) {
    case 0:
      return `${prefix} Unknown Artists`
    case 1:
      return `${prefix} ${first(artists)}`
    case 2:
      return `${prefix} ${join(artists, ' and ')}`
    case 3:
      return `${prefix} ${artists.slice(0, 2).join(', ')} and ${last(artists)}`
    default:
      return `${prefix} ${artists.slice(0, 3).join(', ')} and ${artists.length - 3} other artists`
  }
}

export const aggregateDuration = (tracks) => sum(tracks.map((t) => t.length))

export const aggregateDiscsCount = (tracks) =>
  new Set(tracks.map((t) => t.disc_number).filter(isNumber)).size

export const isVariousArtists = (tracks) => tracks.some((t) => t.album_artist !== t.track_artist)

export const aggregateAlbum = (tracks) => {
  if (tracks.length === 0) {
    throw new Error('Could not aggregate empty track list')
  }

  return {
    album_title: aggregateAlbumTitle(tracks),
    album_url: tracks.map((t) => t.album_url).reduce(or, ''),
    album_artist: tracks.map((t) => t.album_artist).reduce(or, ''),
    cover_id: tracks.map((t) => t.middle_cover_id).reduce(or, null),
    artist_url: tracks.map((t) => t.artist_url).reduce(or),
    album_year: aggregateYears(tracks),
    album_genre: aggregateGenres(tracks),
    length: aggregateDuration(tracks),
    discs_count: aggregateDiscsCount(tracks),
    is_various: isVariousArtists(tracks),
    tracks,
  }
}
