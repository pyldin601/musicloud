<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:28
 */

namespace app\project\persistence\db\tables;


class AudiosTable {

    const TABLE_NAME            = "audios";

    const ID                    = "id";
    const CREATED_DATE          = "created_date";
    const USER_ID               = "user_id";
    const FILE_ID               = "file_id";
    const FILE_NAME             = "file_name";
    const CONTENT_TYPE          = "content_type";

    const ID_FULL               = self::TABLE_NAME . "." . self::ID;
    const CREATED_DATE_FULL     = self::TABLE_NAME . "." . self::CREATED_DATE;
    const USER_ID_FULL          = self::TABLE_NAME . "." . self::USER_ID;
    const FILE_ID_FULL          = self::TABLE_NAME . "." . self::FILE_ID;
    const FILE_NAME_FULL        = self::TABLE_NAME . "." . self::FILE_NAME;
    const CONTENT_TYPE_FULL     = self::TABLE_NAME . "." . self::CONTENT_TYPE;

}