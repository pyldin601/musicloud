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

class DoAddTracks implements RouteHandler {
    public function doGet(JsonResponse $response, $playlist_id, $track_id) {
        $playlist = new Playlist($playlist_id);
        $playlist->addTracks($track_id);
    }
} 