<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 12.08.2015
 * Time: 16:10
 */

namespace app\project\handlers\fixed\api\track;


use app\core\cache\TempFileProvider;
use app\core\http\HttpFiles;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;
use app\project\models\tracklist\Songs;

class DoArtwork implements RouteHandler {
    public function doPost(JsonResponse $response, LoggedIn $me, HttpFiles $files, $track_id) {
        $artwork_file = $files->getOrError("artwork_file");
        $temp_image = TempFileProvider::generate("", $artwork_file["name"]);

        move_uploaded_file($artwork_file["tmp_name"], $temp_image);

        $response->write(Songs::changeCover($track_id, $temp_image));
    }
} 