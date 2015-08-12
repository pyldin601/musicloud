<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 12.08.15
 * Time: 20:02
 */

namespace app\project\handlers\fixed\api\track;


use app\core\exceptions\ControllerException;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\libs\AudioScrobbler;
use app\project\models\tracklist\Songs;

class DoFindAlbumArtwork implements RouteHandler {
    public function doGet(JsonResponse $response, $track_id, $artist, $album) {
        $scrobbler = new AudioScrobbler();
        $cover = $scrobbler->getAlbumCover($artist, $album);
        if ($cover->nonEmpty()) {
            $response->write(Songs::changeCover($track_id, $cover->get()));
        } else {
            throw new ControllerException("Artwork not found");
        }
    }
}