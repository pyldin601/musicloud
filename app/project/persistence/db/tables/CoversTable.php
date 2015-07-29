<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 13:42
 */

namespace app\project\persistence\db\tables;


class CoversTable {

    const TABLE_NAME = "covers";

    const ID = "id";
    const COVER_SMALL = "cover_small";
    const COVER_MIDDLE = "cover_middle";
    const COVER_FULL = "cover_full";

    const ID_FULL = self::TABLE_NAME . "." . self::ID;
    const COVER_SMALL_FULL = self::TABLE_NAME . "." . self::COVER_SMALL;
    const COVER_MIDDLE_FULL = self::TABLE_NAME . "." . self::COVER_MIDDLE;
    const COVER_FULL_FULL = self::TABLE_NAME . "." . self::COVER_FULL;

} 