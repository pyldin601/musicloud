<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\project\handlers\dynamic\catalog;
use app\project\handlers\dynamic\content\DoReadCover;
use app\project\handlers\dynamic\content\DoReadTrack;


when("content/track/&id", DoReadTrack::class);
when("content/cover/&id", DoReadCover::class);

when("api/catalog/tracks/by-artist/:artist", catalog\DoTracksByAlbumArtist::class);
when("api/catalog/tracks/by-album/:album",   catalog\DoTracksByAlbum::class);
when("api/catalog/tracks/by-genre/:genre",   catalog\DoTracksByGenre::class);

//when("test", function (HttpServer $server) {
//    echo $server->getContentType();
//});