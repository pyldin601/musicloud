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
use app\lang\option\Option;
use app\project\models\tracklist\Song;
use app\project\models\tracklist\Songs;

class DoUpload implements RouteHandler {
    public function doPost(JsonResponse $response, Option $track_id, HttpFile $file) {

        $track = $file->getOrError("file");

        $tm = new Song($track_id->orCall([Songs::class, "create"]));

        $response->write($tm->upload($track["tmp_name"], urldecode($track["name"])));

    }
} 