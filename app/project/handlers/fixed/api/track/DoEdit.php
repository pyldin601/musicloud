<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 09.08.15
 * Time: 14:23
 */

namespace app\project\handlers\fixed\api\track;


use app\core\exceptions\ControllerException;
use app\core\http\HttpPost;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Songs;

class DoEdit implements RouteHandler {
    public function doPost(JsonResponse $response, HttpPost $post) {

        $song_id = $post->get("song_id")->reject("")->get();
        $metadata = $post->get("metadata")->filter("is_array")->get();

        $result = (count($metadata) == 0) ? array() : Songs::edit($song_id, $metadata);

        $response->write(["tracks" => $result]);

    }
}