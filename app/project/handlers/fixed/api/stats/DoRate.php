<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 04.08.2015
 * Time: 12:11
 */

namespace app\project\handlers\fixed\api\stats;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoRate implements RouteHandler {
    public function doPost(JsonResponse $response, $id, $rating) {
        $track = new Song($id);
        $track->setRating($rating);
    }
} 