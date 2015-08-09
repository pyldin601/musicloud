<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 16:47
 */

namespace app\project\handlers\fixed\api\track;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Songs;

class DoDelete implements RouteHandler {
    public function doPost(JsonResponse $response, $song_id) {
        Songs::delete($song_id);
    }
} 