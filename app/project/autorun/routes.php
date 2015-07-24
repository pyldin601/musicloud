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
use app\project\handlers\dynamic\content\DoReadCover;
use app\project\handlers\dynamic\content\DoReadTrack;
use app\project\persistence\db\dao\AlbumDao;
use app\project\persistence\db\tables\MetadataTable;


when("content/track/&id", DoReadTrack::class);
when("content/cover/&id", DoReadCover::class);

when("api/catalog/tracks/by-artist/:artist", catalog\DoTracksByAlbumArtist::class);
when("api/catalog/tracks/by-album/:artist/:album", catalog\DoTracksByAlbum::class);
when("api/catalog/tracks/by-genre/:genre", catalog\DoTracksByGenre::class);

when("api/catalog/albums/by-artist/:artist", catalog\DoAlbumsByAlbumArtist::class);

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