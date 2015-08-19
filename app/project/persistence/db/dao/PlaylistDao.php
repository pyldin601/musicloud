<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 16:53
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\exceptions\ControllerException;
use app\project\persistence\db\tables\TPlaylists;

class PlaylistDao {

    /**
     * @param array $data
     * @return array
     */
    public static function create(array $data) {
        return (new InsertQuery(TPlaylists::_NAME))
            ->values($data)
            ->returning("*")
            ->fetchOneRow()
            ->get();
    }

    /**
     * @param $id
     * @param array $data
     * @return array
     */
    public static function update($id, array $data) {
        return (new UpdateQuery(TPlaylists::_NAME, TPlaylists::ID, $id))
            ->set($data)
            ->returning("*")
            ->fetchAll();
    }

    /**
     * @param $id
     */
    public static function delete($id) {
        (new DeleteQuery(TPlaylists::_NAME, TPlaylists::ID, $id))
            ->update();
    }

    /**
     * @param $id
     * @return array
     */
    public static function get($id) {
        return (new SelectQuery(TPlaylists::_NAME, TPlaylists::ID, $id))
            ->fetchOneRow()
            ->getOrThrow(ControllerException::class, "Playlist with id " . $id . " not exists");
    }

    /**
     * @param array $data
     * @return array
     */
    public static function getList(array $data) {
        return (new SelectQuery(TPlaylists::_NAME))
            ->where($data)
            ->fetchAll();
    }

} 