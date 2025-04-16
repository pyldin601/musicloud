<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */

/** REST resources */
when("api/resources/track/:id", app\project\handlers\dynamic\resources\DoTrack::class);

when("preview/:id", app\project\handlers\dynamic\content\DoGetPreview::class);
when("file/:id", app\project\handlers\dynamic\content\DoGetFile::class);
when("peaks/:id", app\project\handlers\dynamic\content\DoWavePeaks::class);

when("lastfm-callback", app\project\handlers\dynamic\scrobbler\DoCallback::class);

whenRegExp("/library\\/.+/", app\project\handlers\fixed\DoLibrary::class);

when("command-for-alexa/:id", app\project\handlers\dynamic\content\DoGetCommandForAlexa::class);
