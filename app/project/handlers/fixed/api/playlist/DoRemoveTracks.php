<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 16:41
 */

namespace app\project\handlers\fixed\api\playlist;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\MLArray;
use app\project\models\Playlist;

class DoRemoveTracks implements RouteHandler {
    public static function doPost(JsonResponse $response, $link_id) {
        Playlist::removeLinks(MLArray::split(",", $link_id));
    }
} 