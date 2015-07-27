<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\project\handlers\dynamic\catalog;
use app\project\handlers\dynamic\content;
use app\project\handlers\fixed\DoLibrary;


when("content/track/&id", content\DoReadTrack::class);
when("content/cover/&id", content\DoReadCover::class);
when("content/raw/&id",   content\DoReadRawContent::class);

when("api/catalog/tracks/by-artist/:artist", catalog\DoTracksByAlbumArtist::class);
when("api/catalog/tracks/by-album/:artist/:album", catalog\DoTracksByAlbum::class);
when("api/catalog/tracks/by-genre/:genre", catalog\DoTracksByGenre::class);

when("api/catalog/albums/by-artist/:artist", catalog\DoAlbumsByAlbumArtist::class);

//whenRegExp("/library\\/.+/", DoLibrary::class);

when("test", function () {
//    (new SelectQuery(MetadataTable::TABLE_NAME))
//        ->where(MetadataTable::ALBUM_ID . " IS NULL")
//        ->eachRow(function ($row) {
//            $album_id = AlbumDao::getAlbumId($row[MetadataTable::ARTIST_ID], $row[MetadataTable::ALBUM]);
//            (new UpdateQuery(MetadataTable::TABLE_NAME))
//                ->set(MetadataTable::ALBUM_ID, $album_id)
//                ->where(MetadataTable::ID, $row[MetadataTable::ID])
//                ->update();
//        });
});