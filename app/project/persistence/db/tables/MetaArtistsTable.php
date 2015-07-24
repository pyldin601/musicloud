<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 23.07.15
 * Time: 21:20
 */

namespace app\project\persistence\db\tables;


class MetaArtistsTable {

    const TABLE_NAME        = "meta_artists";

    const ID                = "id";
    const ARTIST            = "artist";

    const ID_FULL           = self::TABLE_NAME . "." . self::ID;
    const ARTIST_FULL       = self::TABLE_NAME . "." . self::ARTIST;

}