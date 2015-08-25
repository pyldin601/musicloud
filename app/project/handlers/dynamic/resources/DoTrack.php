<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 25.08.2015
 * Time: 15:43
 */

namespace app\project\handlers\dynamic\resources;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoTrack implements RouteHandler {
    public function doGet(JsonResponse $response, $id) {

        $song = new Song($id);

        $response->write($song);

    }
} 