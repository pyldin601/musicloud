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
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\StatsTable;

class CatalogTools {

    /** @var LoggedIn */
    private static $me;

    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    public static function commonSelectors(SelectQuery $query) {
        $query->select(
            MetadataTable::TITLE_FULL,
            MetadataTable::ARTIST_FULL,
            MetadataTable::ALBUM_ID_FULL,
            MetadataTable::ARTIST_ID_FULL,
            MetadataTable::GENRE_ID_FULL,
            MetadataTable::BITRATE_FULL,
            MetadataTable::DATE_FULL,
            MetadataTable::DURATION_FULL,
            MetadataTable::ID_FULL,
            MetadataTable::RATING_FULL,
            MetadataTable::TRACK_NUMBER_FULL,
            AudiosTable::FILE_ID_FULL,
            AudiosTable::FILE_NAME_FULL,
            AudiosTable::CREATED_DATE_FULL,
            StatsTable::LAST_PLAYED_DATE_FULL,
            StatsTable::PLAYBACKS_FULL,
            StatsTable::SKIPS_FULL,
            CoversTable::COVER_MIDDLE_FULL,
            CoversTable::COVER_FULL_FULL,
            CoversTable::COVER_SMALL_FULL
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

    public static function filterArtists(SelectQuery $query) {

        $query->where(
            sprintf("EXISTS (SELECT %s FROM %s INNER JOIN %s USING (id) WHERE %s = %s AND %s = %s)",
                MetadataTable::ARTIST_ID_FULL, MetadataTable::TABLE_NAME, AudiosTable::TABLE_NAME,
                MetadataTable::ARTIST_ID_FULL, MetaArtistsTable::ID_FULL,
                AudiosTable::USER_ID_FULL, self::$me->getId())
        );

    }

    public static function filterGenres(SelectQuery $query) {

        $query->where(
            sprintf("EXISTS (SELECT %s FROM %s INNER JOIN %s USING (id) WHERE %s = %s AND %s = %s)",
                MetadataTable::GENRE_ID_FULL, MetadataTable::TABLE_NAME, AudiosTable::TABLE_NAME,
                MetadataTable::GENRE_ID_FULL, MetaGenresTable::ID_FULL,
                AudiosTable::USER_ID_FULL, self::$me->getId())
        );

    }

    public static function filterAlbums(SelectQuery $query) {

        $query->where(
            sprintf("EXISTS (SELECT %s FROM %s INNER JOIN %s USING (id) WHERE %s = %s AND %s = %s)",
                MetadataTable::ALBUM_ID_FULL, MetadataTable::TABLE_NAME, AudiosTable::TABLE_NAME,
                MetadataTable::ALBUM_ID_FULL, MetaAlbumsTable::ID_FULL,
                AudiosTable::USER_ID_FULL, self::$me->getId())
        );

    }

}