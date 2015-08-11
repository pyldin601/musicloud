/**
 * Created by roman on 08.08.15.
 */

var homecloud = angular.module("HomeCloud");

homecloud.controller("UploadController", ["$rootScope", "TrackService", "$route",

    function ($rootScope, TrackService, $route) {
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
                        selector.attr("accept", "audio/*;audio/flac");
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
                                    upload.action.next();
                                });
                            }).error(function () {
                                console.error("Track(s) could not be uploaded");
                            });

                        });

                    }
                }
            };
        $rootScope.upload = upload;
    }
]);