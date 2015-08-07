<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 07.08.2015
 * Time: 16:32
 */

namespace app\project\handlers\fixed\api\scrobbler;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\libs\AudioScrobbler;
use app\project\persistence\db\dao\SongDao;

class DoScrobble implements RouteHandler {
    public function doPost(JsonResponse $response, $id) {
        $song = SongDao::getSongUsingId($id);
        $scrobbler = new AudioScrobbler();
        $scrobbler->scrobble($song["track_title"], $song["track_artist"]);
    }
} 