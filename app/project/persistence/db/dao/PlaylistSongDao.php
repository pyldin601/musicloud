<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 17:54
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\DeleteQuery;
use app\project\persistence\db\tables\TPlaylistSongLinks;

class PlaylistSongDao {
    public static function delete(array $data) {
        (new DeleteQuery(TPlaylistSongLinks::_NAME))
            ->where($data)
            ->update();
    }
} 