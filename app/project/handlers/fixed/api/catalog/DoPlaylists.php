<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 17:34
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Mapper;
use app\project\models\Playlist;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\dao\PlaylistDao;
use app\project\persistence\db\tables\TPlaylists;

class DoPlaylists implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {
        $playlists = PlaylistDao::getList([ TPlaylists::USER_ID => $me->getId() ]);
        $playlist_objects = $playlists->map(Mapper::call(Playlist::class, "new"));
        $response->write($playlist_objects);
    }
} 