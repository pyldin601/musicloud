<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\project\handlers\dynamic\catalog;
use app\project\handlers\dynamic\content;
use app\project\handlers\fixed\DoLibrary;
use app\project\persistence\db\tables\FilesTable;
use app\project\persistence\fs\FileServer;


when("content/track/&id", content\DoReadTrack::class);
when("content/cover/&id", content\DoReadCover::class);
when("content/peaks/&id", content\DoWavePeaks::class);

when("file/:id",          content\DoGetFile::class);

when("api/catalog/tracks/by-artist/:artist", catalog\DoTracksByAlbumArtist::class);
when("api/catalog/tracks/by-album/:artist/:album", catalog\DoTracksByAlbum::class);
when("api/catalog/tracks/by-genre/:genre", catalog\DoTracksByGenre::class);

when("api/catalog/albums/by-artist/:artist", catalog\DoAlbumsByAlbumArtist::class);

//whenRegExp("/library\\/.+/", DoLibrary::class);

when("test", function () {
//    $files = (new SelectQuery(FilesTable::TABLE_NAME))->where(FilesTable::UNIQUE_ID, "")->fetchAll();
//    foreach ($files as $file) {
//        (new UpdateQuery(FilesTable::TABLE_NAME))
//            ->where(FilesTable::ID, $file[FilesTable::ID])
//            ->set(FilesTable::UNIQUE_ID, FileServer::generateKey())
//            ->update();
//    }
});