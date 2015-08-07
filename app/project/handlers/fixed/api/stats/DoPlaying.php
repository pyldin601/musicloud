<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 07.08.2015
 * Time: 15:31
 */

namespace app\project\handlers\fixed\api\stats;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoPlaying implements RouteHandler {
    public function doPost(JsonResponse $response, $id) {
        $song = new Song($id);
        $song->playingStarted();
    }
} 