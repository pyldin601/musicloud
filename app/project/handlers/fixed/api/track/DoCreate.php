<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:21
 */

namespace app\project\handlers\fixed\api\track;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Tracks;

class DoCreate implements RouteHandler {
    public function doPost(JsonResponse $response) {
        $response->write(Tracks::create(), 201);
    }
} 