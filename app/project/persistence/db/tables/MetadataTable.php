<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:30
 */

namespace app\project\persistence\db\tables;


class MetadataTable {

    const TABLE_NAME            = "metadata";

    const ID                    = "id";
    const ALBUM_ARTIST          = "album_artist";
    const ARTIST                = "artist";
    const TITLE                 = "title";
    const TRACK_NUMBER          = "track_number";
    const ALBUM                 = "album";
    const GENRE                 = "genre";
    const DATE                  = "date";
    const RATING                = "rating";
    const COVER_FILE_ID         = "cover_file_id";
    const WAVEFORM              = "waveform";
    const BITRATE               = "bitrate";
    const DURATION              = "duration";
    const GENRE_ID              = "genre_id";
    const ARTIST_ID             = "artist_id";
    const ALBUM_ID              = "album_id";

    const ID_FULL               = self::TABLE_NAME . "." .self::ID;
    const ALBUM_ARTIST_FULL     = self::TABLE_NAME . "." .self::ALBUM_ARTIST;
    const ARTIST_FULL           = self::TABLE_NAME . "." .self::ARTIST;
    const TITLE_FULL            = self::TABLE_NAME . "." .self::TITLE;
    const TRACK_NUMBER_FULL     = self::TABLE_NAME . "." .self::TRACK_NUMBER;
    const ALBUM_FULL            = self::TABLE_NAME . "." .self::ALBUM;
    const GENRE_FULL            = self::TABLE_NAME . "." .self::GENRE;
    const DATE_FULL             = self::TABLE_NAME . "." .self::DATE;
    const RATING_FULL           = self::TABLE_NAME . "." .self::RATING;
    const COVER_FILE_ID_FULL    = self::TABLE_NAME . "." .self::COVER_FILE_ID;
    const WAVEFORM_FULL         = self::TABLE_NAME . "." .self::WAVEFORM;
    const BITRATE_FULL          = self::TABLE_NAME . "." .self::BITRATE;
    const DURATION_FULL         = self::TABLE_NAME . "." .self::DURATION;
    const GENRE_ID_FULL         = self::TABLE_NAME . "." .self::GENRE_ID;
    const ARTIST_ID_FULL        = self::TABLE_NAME . "." .self::ARTIST_ID;
    const ALBUM_ID_FULL         = self::TABLE_NAME . "." .self::ALBUM_ID;

} 