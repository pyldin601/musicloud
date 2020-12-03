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
import { IHttpService } from 'angular'
import qs from 'qs'

const CONTENT_TYPE = 'application/x-www-form-urlencoded;charset=utf-8'

export interface Track {
  album_artist: string
  big_cover_id: null | string
  bitrate: number
  created_date: number
  disc_number: null | number
  file_id: string
  file_name: string
  format: string
  id: string
  is_compilation: boolean
  is_favourite: boolean
  last_played_date: null | number
  length: number
  middle_cover_id: null | string
  small_cover_id: null | string
  times_played: number
  times_skipped: number
  track_album: string
  track_artist: string
  track_comment: string
  track_genre: string
  track_number: number
  track_rating: null | number
  track_title: string
  track_year: string
}

class TrackService {
  constructor(private $http: IHttpService) {}

  public async create(): Promise<string> {
    const response = await this.$http.post<string>('/api/track/create', undefined)
    return response.data
  }

  public async upload(
    { file, trackId }: { readonly file: File; readonly trackId: string },
    callback: (ev: ProgressEvent) => void,
  ) {
    const formData = new FormData()
    formData.set('file', file)
    formData.set('track_id', trackId)

    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest()
      xhr.upload.onprogress = callback
      xhr.onload = resolve
      xhr.onerror = reject
      xhr.open('POST', '/api/track/upload', true)
      xhr.send(formData)
    })
  }

  async unlink(trackId: string): Promise<void> {
    const form = qs.stringify({ song_id: trackId })
    await this.$http.post('/api/track/delete', form, {
      headers: {
        'Content-Type': CONTENT_TYPE,
      },
    })
  }

  async getPeaks(trackId: string): Promise<ReadonlyArray<number>> {
    const response = await this.$http.get<ReadonlyArray<number>>(`/peaks/${trackId}`)
    return response.data
  }

  async deleteByArtist({
    trackArtist,
  }: {
    readonly trackArtist: string
  }): Promise<ReadonlyArray<string>> {
    const form = qs.stringify({ track_artist: trackArtist })
    const response = await this.$http.post<ReadonlyArray<string>>('/api/track/deleteByArtist', form)
    return response.data
  }

  public async edit(
    songId: string,
    metadata: {
      readonly track_title?: string
      readonly track_artist?: string
      readonly track_album?: string
      readonly album_artist?: string
      readonly track_number?: string
      readonly disc_number?: string
      readonly track_genre?: string
      readonly track_year?: string
      readonly is_compilation?: boolean
    },
  ): Promise<ReadonlyArray<Track>> {
    const formData = qs.stringify({ song_id: songId, metadata })
    const response = await this.$http.post<{ readonly tracks: ReadonlyArray<Track> }>(
      '/api/track/edit',
      formData,
      {
        headers: {
          'Content-Type': CONTENT_TYPE,
        },
      },
    )
    return response.data.tracks
  }

  public async changeArtwork({
    artworkFile,
    trackId,
  }: {
    readonly artworkFile: File
    readonly trackId: string
  }): Promise<ReadonlyArray<Track>> {
    const form = new FormData()
    form.append('track_id', trackId)
    form.append('artwork_file', artworkFile)

    const response = await this.$http.post<ReadonlyArray<Track>>('/api/track/artwork', form, {
      headers: {
        'Content-Type': undefined,
      },
    })
    return response.data
  }

  public async createFromVideo(video_url: string): Promise<void> {
    await this.$http.post('/api/track/createFromVideo', { video_url })
  }
}

TrackService.$inject = ['$http']

export default TrackService
