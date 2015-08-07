<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 07.08.2015
 * Time: 16:31
 */

namespace app\project\handlers\fixed\api\scrobbler;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\libs\AudioScrobbler;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\TSongs;

class DoNowPlaying implements RouteHandler {
    public function doPost(JsonResponse $response, $id) {
        $song = SongDao::getSongUsingId($id);
        $scrobbler = new AudioScrobbler();
        $scrobbler->nowPlaying($song[TSongs::T_TITLE], $song[TSongs::T_ARTIST], $song[TSongs::T_ALBUM]);
    }
} 