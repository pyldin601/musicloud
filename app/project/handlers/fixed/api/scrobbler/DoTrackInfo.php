<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 07.08.2015
 * Time: 16:08
 */

namespace app\project\handlers\fixed\api\scrobbler;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\libs\AudioScrobbler;
use app\project\persistence\db\dao\SongDao;

class DoTrackInfo implements RouteHandler {
    public function doGet(JsonResponse $response, $id) {
        $song = SongDao::getSongUsingId($id);
        $scrobbler = new AudioScrobbler();
        $track_info = $scrobbler->getTrackInfo($song["track_title"], $song["track_artist"]);
        $response->write($track_info);
    }
} 