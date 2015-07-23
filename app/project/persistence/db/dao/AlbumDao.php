<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 23.07.15
 * Time: 21:37
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\project\persistence\db\tables\MetaAlbumsTable;

class AlbumDao {
    public static function getAlbumId($artist_id, $album) {
        assert(is_numeric($artist_id), "Artist id could not be null");
        $album_object = (new SelectQuery(MetaAlbumsTable::TABLE_NAME))
            ->where(MetaAlbumsTable::ALBUM, $album)
            ->where(MetaAlbumsTable::ARTIST_ID, $artist_id)
            ->fetchOneRow();
        if ($album_object->nonEmpty()) {
            return $album_object->get()[MetaAlbumsTable::ID];
        } else {
            return (new InsertQuery(MetaAlbumsTable::TABLE_NAME))
                ->values(MetaAlbumsTable::ALBUM, $album)
                ->values(MetaAlbumsTable::ARTIST_ID, $artist_id)
                ->executeInsert();
        }
    }
}