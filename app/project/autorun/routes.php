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
use app\project\libs\FFProbe;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\FilesTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\fs\FileServer;


when("content/track/&id", content\DoReadTrack::class);
when("content/cover/&id", content\DoReadCover::class);
when("content/peaks/&id", content\DoWavePeaks::class);

when("file/:id", content\DoGetFile::class);

when("api/catalog/tracks/by-artist/:artist", catalog\DoTracksByAlbumArtist::class);
when("api/catalog/tracks/by-album/:artist/:album", catalog\DoTracksByAlbum::class);
when("api/catalog/tracks/by-genre/:genre", catalog\DoTracksByGenre::class);

when("api/catalog/albums/by-artist/:artist", catalog\DoAlbumsByAlbumArtist::class);

//whenRegExp("/library\\/.+/", DoLibrary::class);

when("test", function () {
    set_time_limit(0);
    $n = 0;
    $query = new SelectQuery(MetadataTable::TABLE_NAME);
    $query->joinUsing(AudiosTable::TABLE_NAME, "id");
    $query->where("disc_number = ''");
    $query->eachRow(function ($file) use (&$n) {
        $f = FileServer::getFileUsingId($file[AudiosTable::FILE_ID]);
        $disc = FFProbe::read($f)->map(function ($e) { return $e->meta_disc_number; });
        if ($disc->nonEmpty()) {
            (new UpdateQuery(MetadataTable::TABLE_NAME, MetadataTable::ID_FULL, $file[MetadataTable::ID]))
                ->set(MetadataTable::DISC_NUMBER_FULL, $disc->get())
                ->update();
        }
        error_log(++$n);
    });
    echo "OK";
});