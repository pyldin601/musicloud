<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 21.08.15
 * Time: 14:42
 */

namespace app\project\handlers\fixed\api\playlist;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\Playlist;

class DoGet implements RouteHandler {
    public function doGet(JsonResponse $response, $playlist_id) {
        $playlist = new Playlist($playlist_id);
        $response->write($playlist);
    }
}