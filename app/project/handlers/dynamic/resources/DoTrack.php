<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 25.08.2015
 * Time: 15:43
 */

namespace app\project\handlers\dynamic\resources;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoTrack implements RouteHandler {
    public function doGet(JsonResponse $response, $id) {

        $song = (new Song($id))->getObject();

        $artist_encoded = escape_url($song["album_artist"]);
        $album_encoded  = escape_url($song["track_album"]);
        $genre_encoded  = escape_url($song["track_genre"]);

        $song["artist_url"] = "artist/{$artist_encoded}";
        $song["album_url"]  = "artist/{$artist_encoded}/{$album_encoded}";
        $song["genre_url"]  = "genre/{$genre_encoded}";

        $response->write($song);

    }
} 