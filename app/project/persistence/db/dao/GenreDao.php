<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 23.07.15
 * Time: 20:41
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\MetaGenresTable;

class GenreDao {
    /** @var LoggedIn */
    private static $me;
    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }
    public static function getGenreId($genre) {
        $genre_object = (new SelectQuery(MetaGenresTable::TABLE_NAME))
            ->where(MetaGenresTable::USER_ID, self::$me->getId())
            ->where(MetaGenresTable::GENRE, $genre)
            ->fetchOneRow();
        if ($genre_object->nonEmpty()) {
            return $genre_object->get()[MetaGenresTable::ID];
        } else {
            return (new InsertQuery(MetaGenresTable::TABLE_NAME))
                ->values(MetaGenresTable::GENRE, $genre)
                ->values(MetaGenresTable::USER_ID, self::$me->getId())
                ->executeInsert();
        }
    }
}