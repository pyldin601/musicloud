<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:22
 */

namespace app\project\models;


use app\core\db\builder\InsertQuery;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;

class Tracks {
    /** @var LoggedIn */
    private static $me;
    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    /**
     * @return int Created track id
     */
    public static function create() {
        $query = new InsertQuery(AudiosTable::TABLE_NAME);
        $query->values(AudiosTable::USER_ID, self::$me->getId());
        $query->values(AudiosTable::CREATED_DATE, time());
        return $query->executeInsert();
    }
}