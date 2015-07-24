<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 23.07.15
 * Time: 20:39
 */

namespace app\project\persistence\db\tables;


class MetaGenresTable {

    const TABLE_NAME        = "meta_genres";

    const ID                = "id";
    const GENRE             = "genre";
    const USER_ID           = "user_id";

    const ID_FULL           = self::TABLE_NAME . "." . self::ID;
    const GENRE_FULL        = self::TABLE_NAME . "." . self::GENRE;
    const USER_ID_FULL      = self::TABLE_NAME . "." . self::USER_ID;

}