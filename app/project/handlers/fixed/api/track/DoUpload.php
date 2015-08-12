<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:31
 */

namespace app\project\handlers\fixed\api\track;


use app\core\http\HttpFiles;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Option;
use app\project\models\tracklist\Song;
use app\project\models\tracklist\Songs;

class DoUpload implements RouteHandler {
    public function doPost(JsonResponse $response, $track_id, HttpFiles $file) {

        $track = $file->getOrError("file");

        $tm = new Song($track_id);

        $response->write($tm->upload($track["tmp_name"], urldecode($track["name"])));

    }
} 