<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 17:54
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\project\persistence\db\tables\TPlaylistSongLinks;

class PlaylistSongDao {

    public static function delete(array $data) {
        (new DeleteQuery(TPlaylistSongLinks::_NAME))
            ->where($data)
            ->update();
    }

    /**
     * @param array $data
     * @return array
     */
    public static function getList(array $data) {
        return (new SelectQuery(TPlaylistSongLinks::_NAME))
            ->where($data)
            ->fetchAll();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function create(array $data) {
        return (new InsertQuery(TPlaylistSongLinks::_NAME))
            ->values($data)
            ->returning("*")
            ->fetchOneRow()
            ->get();
    }
} 