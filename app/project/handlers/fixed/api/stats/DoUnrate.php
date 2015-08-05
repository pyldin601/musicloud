<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 04.08.2015
 * Time: 12:23
 */

namespace app\project\handlers\fixed\api\stats;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoUnrate implements RouteHandler {
    public function doPost(JsonResponse $response, $id) {
        $track = new Song($id);
        $track->removeRating();
    }
} 