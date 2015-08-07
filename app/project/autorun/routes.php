<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


when("preview/:id", app\project\handlers\dynamic\content\DoGetPreview::class);
when("file/:id",    app\project\handlers\dynamic\content\DoGetFile::class);
when("peaks/:id",   app\project\handlers\dynamic\content\DoWavePeaks::class);

when("lastfm-callback", app\project\handlers\dynamic\scrobbler\DoCallback::class);
