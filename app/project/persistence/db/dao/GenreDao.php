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
use app\project\persistence\db\tables\MetaGenresTable;

/**
 * Class GenreDao
 * @package app\project\persistence\db\dao
 */
class GenreDao {

    /**
     * Returns id of $genre from database if exists, or generates
     * new row for $genre and returns generated id.
     *
     * @param $genre
     * @return int
     */
    public static function getGenreId($genre) {
        expect_string($genre);

        $genre_object = (new SelectQuery(MetaGenresTable::TABLE_NAME))
            ->where(MetaGenresTable::GENRE, $genre)
            ->select(MetaGenresTable::ID)
            ->fetchOneColumn()->toInt();
        if ($genre_object->nonEmpty()) {
            return $genre_object->get();
        } else {
            return (new InsertQuery(MetaGenresTable::TABLE_NAME))
                ->values(MetaGenresTable::GENRE, $genre)
                ->executeInsert();
        }
    }

}