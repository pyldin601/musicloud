<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 16:40
 */

namespace app\project\handlers\fixed\api\playlist;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\Playlist;

class DoDelete implements RouteHandler {
    public function doPost(JsonResponse $response, $id) {
        $playlist = new Playlist($id);
        $playlist->delete();
    }
} 