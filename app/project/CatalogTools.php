<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:40
 */

namespace app\project;


use app\core\db\builder\SelectQuery;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\StatsTable;

class CatalogTools {

    /** @var LoggedIn */
    private static $me;

    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    public static function commonSelectors(SelectQuery $query) {
        $query->select(
            MetadataTable::ALBUM,
            MetadataTable::TITLE,
            MetadataTable::ARTIST,
            MetadataTable::ALBUM_ARTIST,
            MetadataTable::BITRATE,
            MetadataTable::DATE,
            MetadataTable::DURATION,
            MetadataTable::GENRE,
            MetadataTable::ID_FULL,
            MetadataTable::RATING,
            MetadataTable::TRACK_NUMBER,
            MetadataTable::COVER_FILE_ID,
            AudiosTable::CREATED_DATE,
            StatsTable::LAST_PLAYED_DATE,
            StatsTable::PLAYBACKS,
            StatsTable::SKIPS
        );
    }

    public static function commonSelectAlbum(SelectQuery $query) {
        $query->select(
            MetadataTable::ALBUM_FULL,
            MetadataTable::DATE_FULL,
            MetadataTable::COVER_FILE_ID_FULL
        );
    }

    public static function commonSelectArtist(SelectQuery $query) {
        $query->select(
//            "COUNT(distinct ".MetadataTable::ALBUM.") as albums_count",
//            "COUNT(distinct ".MetadataTable::TABLE_NAME.".".MetadataTable::ID.") as tracks_count",
//            MetadataTable::COVER_FILE_ID
        );
    }

    /**
     * @param string $key
     * @param array $object
     * @return array
     */
    public static function descent($key, array $object) {
        $result = array();
        foreach ($object as $item) {
            if (!isset($result[$item[$key]])) {
                $result[$item[$key]] = array();
            }
            $result[$item[$key]][] = $item;
        }
        return $result;
    }

    public static function filterArtist(SelectQuery $query) {

        $query->where(
            sprintf("EXISTS (SELECT %s FROM %s INNER JOIN %s USING (id) WHERE %s = %s AND %s = %s)",
                MetadataTable::ARTIST_ID_FULL, MetadataTable::TABLE_NAME, AudiosTable::TABLE_NAME,
                MetadataTable::ARTIST_ID_FULL, MetaArtistsTable::ID_FULL,
                AudiosTable::USER_ID_FULL, self::$me->getId())
        );

    }
} 