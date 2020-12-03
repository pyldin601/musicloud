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
import angular, { IHttpService } from 'angular'
import { makeUrlEncodedForm } from './utils'

const CONTENT_TYPE = 'application/x-www-form-urlencoded;charset=utf-8'

interface UploadFormData {
  file: File
  trackId: string
}

class TrackService {
  constructor(private $http: IHttpService) {}

  public async create(): Promise<string> {
    const response = await this.$http.post<string>('/api/track/create', undefined)
    return response.data
  }

  public async upload(data: UploadFormData, callback: (ev: ProgressEvent) => void) {
    const formData = new FormData()
    formData.set('file', data.file)
    formData.set('track_id', data.trackId)
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest()
      xhr.upload.onprogress = callback
      xhr.onload = resolve
      xhr.onerror = () => reject(xhr.response)
      xhr.open('POST', '/api/track/upload', true)
      xhr.send(formData)
    })
  }

  async unlink(songId: string): Promise<void> {
    await this.$http.post('/api/track/delete', makeUrlEncodedForm({ song_id: songId }), {
      headers: {
        'Content-Type': CONTENT_TYPE,
      },
    })
  }

  async getPeaks(trackId: string): Promise<ReadonlyArray<number>> {
    const response = await this.$http.get<ReadonlyArray<number>>(`/peaks/${trackId}`)
    return response.data
  }

  deleteByArtist = (data: unknown) => this.$http.post('/api/track/deleteByArtist', data)
  edit = (data: unknown) => this.$http.post('/api/track/edit', data)

  changeArtwork = (data: unknown) =>
    this.$http.post('/api/track/artwork', data, {
      transformRequest: angular.identity,
      headers: {
        'Content-Type': undefined,
      },
    })
  createFromVideo = (video_url: string) =>
    this.$http.post('/api/track/createFromVideo', { video_url })
  getQueue = () => this.$http.get('/api/track/queue')
}

TrackService.$inject = ['$http']

export default TrackService
