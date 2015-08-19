<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 16:41
 */

namespace app\project\handlers\fixed\api\playlist;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\Playlist;

class DoRemoveTracks implements RouteHandler {
    public static function doPost(JsonResponse $response, $playlist_id, $link_id) {
        $ids_array = explode(",", $link_id);
        $playlist = new Playlist($playlist_id);
        $playlist->removeTracks($ids_array);
    }
} 