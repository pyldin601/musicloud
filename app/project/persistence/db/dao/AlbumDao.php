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
use app\lang\option\Filter;
use app\lang\option\Mapper;
use app\project\persistence\db\tables\MetaAlbumsTable;

class AlbumDao {
    public static function getAlbumId($artist_id, $album) {
        expect_number($artist_id);
        expect_string($album);

        $album_object = (new SelectQuery(MetaAlbumsTable::TABLE_NAME))
            ->where(MetaAlbumsTable::ALBUM, $album)
            ->where(MetaAlbumsTable::ARTIST_ID, $artist_id)
            ->select(MetaAlbumsTable::ID)
            ->fetchOneColumn()->toInt();
        if ($album_object->nonEmpty()) {
            return $album_object->get();
        } else {
            return (new InsertQuery(MetaAlbumsTable::TABLE_NAME))
                ->values(MetaAlbumsTable::ALBUM, $album)
                ->values(MetaAlbumsTable::ARTIST_ID, $artist_id)
                ->executeInsert();
        }
    }
}