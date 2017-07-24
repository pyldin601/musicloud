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

export default ["$rootScope", "TrackService", "$route", ($rootScope, TrackService, $route) => {
    var progress = function (event) {
        if (event.lengthComputable) {
          $rootScope.$applyAsync(upload.data.progress.percent = parseInt(event.loaded / event.total * 100));
        }
      },
      upload = {
        data: {
          queue: [],
          current: null,
          uploading: false,
          promise: null,
          progress: {
            percent: 0
          }
        },
        action: {
          add: function () {
            var selector = $("<input>");
            selector.attr("type", "file");
            selector.attr("accept", "audio/mpeg,audio/aac,audio/flac,audio/ogg");
            selector.attr("multiple", "multiple");
            selector.attr("name", "file");
            selector.on("change", function () {
              if (this.files.length == 0) return;
              var that = this;
              $rootScope.$applyAsync(function () {
                for (var i = 0; i < that.files.length; i++) {
                  if (that.files[i].size > maxFileSize) {
                    // todo: warn if file size is too big
                    continue;
                  }
                  upload.data.queue.push(that.files[i]);
                }
              });
              upload.action.next();
            });
            selector.click();
          },
          addDirectory: function () {
            var selector = $("<input>");
            selector.attr("type", "file");
            selector.attr("accept", "audio/mpeg,audio/aac,audio/flac,audio/ogg");
            selector.attr("multiple", "multiple");
            selector.attr("webkitdirectory", "");
            selector.attr("directory", "");
            selector.attr("name", "file");
            selector.on("change", function () {
              if (this.files.length == 0) return;
              var that = this;
              $rootScope.$applyAsync(function () {
                for (var i = 0; i < that.files.length; i++) {
                  if (that.files[i].size > maxFileSize) {
                    // todo: warn if file size is too big
                    continue;
                  }
                  upload.data.queue.push(that.files[i]);
                }
              });
              upload.action.next();
            });
            selector.click();
          },
          cancel: function () {
            if (confirm("Are you sure want to cancel current uploading?")) {
              if (upload.data.promise !== null) {
                upload.data.promise.abort();
              }
              upload.action.clean();
            }
          },
          clean: function () {
            upload.data.uploading = false;
            upload.data.current = null;
            upload.data.progress.percent = 0;
            upload.data.promise = null;
            upload.data.queue = [];
            $route.reload();
          },
          next: function () {

            $rootScope.$applyAsync(function () {

              if (upload.data.queue.length == 0) {
                upload.action.clean();
                return;
              } else {
                upload.data.uploading = true;
                upload.data.current = upload.data.queue.shift();
              }

              TrackService.create().success(function (id) {
                var form = new FormData();
                form.append("file", upload.data.current);
                form.append("track_id", id);
                upload.data.promise = TrackService.upload(form, progress);
                upload.data.promise.success(function (data) {
                  upload.action.next();
                }).error(function () {
                  TrackService.unlink({ song_id: id }).then(function () {
                    return upload.action.next();
                  });
                });
              });

            });

          }
        }
      };
    $rootScope.upload = upload;
  }
];