<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 23.07.15
 * Time: 21:20
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;

class ArtistDao {

    /** @var LoggedIn */
    private static $me;

    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    /**
     * Returns id of $artist from database if exists, or generates
     * new row and returns generated id.
     *
     * @param $artist
     * @return int
     */
    public static function getArtistId($artist) {
        expect_string($artist);

        $artist_object = (new SelectQuery(MetaArtistsTable::TABLE_NAME))
            ->where(MetaArtistsTable::ARTIST, $artist)
            ->where(MetaArtistsTable::USER_ID, self::$me->getId())
            ->select(MetaAlbumsTable::ID)
            ->fetchOneColumn()->toInt();
        if ($artist_object->nonEmpty()) {
            return $artist_object->get();
        } else {
            return (new InsertQuery(MetaArtistsTable::TABLE_NAME))
                ->values(MetaArtistsTable::ARTIST, $artist)
                ->values(MetaArtistsTable::USER_ID, self::$me->getId())
                ->executeInsert();
        }
    }

}