<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:31
 */

namespace app\project\handlers\fixed\api\track;


use app\core\cache\TempFileProvider;
use app\core\http\HttpFiles;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoUpload implements RouteHandler {
    public function doPost(JsonResponse $response, $track_id, HttpFiles $file) {

        $track = $file->getOrError("file");

        $decoded_name = urldecode($track["name"]);
        $extension = pathinfo($decoded_name, PATHINFO_EXTENSION);

        $tm = new Song($track_id);

        $temp_file = TempFileProvider::generate("upload", ".$extension");

        error_log(print_r($track, true));

        error_log("Old Exists: " . (file_exists($track["tmp_name"]) ? 1 : 0));

        move_uploaded_file($track["tmp_name"], $temp_file);

        error_log("New Exists: " . (file_exists($temp_file) ? 1 : 0));

        $response->write($tm->upload($temp_file, $decoded_name));

    }
} 