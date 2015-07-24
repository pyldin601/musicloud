<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 23.07.15
 * Time: 21:47
 */

namespace app\project\persistence\db\tables;


class MetaAlbumsTable {

    const TABLE_NAME            = "meta_albums";

    const ID                    = "id";
    const ARTIST_ID             = "artist_id";
    const ALBUM                 = "album";

    const ID_FULL               = self::TABLE_NAME . "." . self::ID;
    const ARTIST_ID_FULL        = self::TABLE_NAME . "." . self::ARTIST_ID;
    const ALBUM_FULL            = self::TABLE_NAME . "." . self::ALBUM;

}