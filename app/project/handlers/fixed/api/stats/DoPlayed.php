<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 29.07.15
 * Time: 21:04
 */

namespace app\project\handlers\fixed\api\stats;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoPlayed implements RouteHandler {
    public function doPost(JsonResponse $response, $id) {
        $track = new Song($id);
        $track->incrementPlays();
    }
}