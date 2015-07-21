<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:31
 */

namespace app\project\handlers\fixed\api\track;


use app\core\http\HttpFile;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Track;

class DoUpload implements RouteHandler {
    public function doPost(JsonResponse $response, $track_id, HttpFile $file) {

        $track = $file->getOrError("file");

        $tm = new Track($track_id);

        $tm->upload($track["tmp_name"], $track["name"], $track["type"]);

    }
} 