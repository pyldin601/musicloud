<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\lang\Tools;
use app\project\handlers\dynamic\catalog;
use app\project\handlers\dynamic\content;


when("content/track/&id", content\DoReadTrack::class);
when("content/cover/&id", content\DoReadCover::class);
when("content/peaks/&id", content\DoWavePeaks::class);

when("file/:id", content\DoGetFile::class);

when("api/catalog/tracks/by-artist/:artist", catalog\DoTracksByAlbumArtist::class);
when("api/catalog/tracks/by-album/:artist/:album", catalog\DoTracksByAlbum::class);
when("api/catalog/tracks/by-genre/:genre", catalog\DoTracksByGenre::class);

when("api/catalog/albums/by-artist/:artist", catalog\DoAlbumsByAlbumArtist::class);

when("test", function () {
    echo Tools::closest(240, [8,16,24,32,48,52,64,96,128,160,192,224,256,320]);
});