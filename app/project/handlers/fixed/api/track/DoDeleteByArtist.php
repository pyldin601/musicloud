<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 24.08.15
 * Time: 17:33
 */

namespace app\project\handlers\fixed\api\track;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Songs;

class DoDeleteByArtist implements RouteHandler {
    public function doPost(JsonResponse $response, $track_artist) {
        $deleted_songs = Songs::deleteByArtist($track_artist);
        $response->write($deleted_songs);
    }
}