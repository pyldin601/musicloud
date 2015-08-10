<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 30.07.15
 * Time: 21:01
 */

namespace app\project\persistence\db\tables;


class TSongs {

    const _NAME = "songs";

    const ID = "id";
    const USER_ID = "user_id";
    const FILE_ID = "file_id";
    const FILE_NAME = "file_name";
    const BITRATE = "bitrate";
    const LENGTH = "length";
    const FORMAT = "format";
    const T_TITLE = "track_title";
    const T_ARTIST = "track_artist";
    const T_ALBUM = "track_album";
    const T_GENRE = "track_genre";
    const T_NUMBER = "track_number";
    const T_COMMENT = "track_comment";
    const T_YEAR = "track_year";
    const T_RATING = "track_rating";
    const IS_FAV = "is_favourite";
    const IS_COMP = "is_compilation";
    const DISC = "disc_number";
    const A_ARTIST = "album_artist";
    const T_PLAYED = "times_played";
    const T_SKIPPED = "times_skipped";
    const C_DATE = "created_date";
    const LP_DATE = "last_played_date";
    const C_SMALL_ID = "small_cover_id";
    const C_MID_ID = "middle_cover_id";
    const C_BIG_ID = "big_cover_id";
    const PREVIEW_ID = "preview_id";
    const PEAKS = "peaks";

    const FTS_ARTIST = "fts_artist";
    const FTS_ALBUM = "fts_album";
    const FTS_ANY = "fts_any";
    const FTS_GENRE = "fts_genre";

    public static function defaultSelection() {
        return implode(",", [
            TSongs::ID, TSongs::FILE_ID, TSongs::FILE_NAME, TSongs::BITRATE,
            TSongs::LENGTH, TSongs::T_TITLE, TSongs::T_ARTIST, TSongs::T_ALBUM, TSongs::T_GENRE,
            TSongs::T_NUMBER, TSongs::T_COMMENT, TSongs::T_YEAR, TSongs::T_RATING, TSongs::IS_FAV,
            TSongs::DISC, TSongs::A_ARTIST, TSongs::T_PLAYED, TSongs::T_SKIPPED, TSongs::LP_DATE,
            TSongs::C_SMALL_ID, TSongs::C_MID_ID, TSongs::C_BIG_ID, TSongs::FORMAT, TSongs::IS_COMP
        ]);
    }

}