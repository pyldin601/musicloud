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
import $ from 'jquery';
import angular from 'angular';

export default ["$http", ($http) => ({
    create: () => $http.post("/api/track/create", {}),
    upload: (data, callback) => {
      return $.ajax({
        xhr: function() {
          const xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", callback, false);
          return xhr;
        },
        url: "/api/track/upload",
        type: "POST",
        data,
        processData: false,
        contentType: false
      });
    },
    unlink: (data) => $http.post("/api/track/delete", data),
    deleteByArtist: (data) => $http.post("/api/track/deleteByArtist", data),
    edit: (data) => $http.post("/api/track/edit", data),
    getPeaks: (id) => $http.get("/peaks/" + id),
    changeArtwork: (data) => $http.post("/api/track/artwork", data, {
      transformRequest: angular.identity,
      headers: {
        'Content-Type': undefined
      }
    }),
    createFromVideo: (video_url: string) => {
      return $http.post('/api/track/createFromVideo', { video_url });
    },
    getQueue: () => {
      return $http.get('/api/track/queue');
    },
  }
)];
