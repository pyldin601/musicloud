<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:40
 */

namespace app\project;


use app\core\db\builder\SelectQuery;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\StatsTable;

class CatalogTools {
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
            MetadataTable::TABLE_NAME.".".MetadataTable::ID,
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
            MetadataTable::ALBUM,
            MetadataTable::DATE,
            MetadataTable::COVER_FILE_ID,
            "SUM(" . MetadataTable::DURATION . ") AS " . MetadataTable::DURATION,
            "AVG(" . MetadataTable::RATING . ") AS " . MetadataTable::RATING
        );
    }

    public static function commonSelectArtist(SelectQuery $query) {
        $query->select(
            MetadataTable::COVER_FILE_ID,
            "SUM(" . MetadataTable::DURATION . ") AS " . MetadataTable::DURATION,
            "AVG(" . MetadataTable::RATING . ") AS " . MetadataTable::RATING
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
} 