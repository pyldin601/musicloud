<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 17:37
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\TPlaylistSongLinks;
use app\project\persistence\db\tables\TSongs;

class DoPlaylistTracks implements RouteHandler {
    public function doGet(LoggedIn $me, $playlist_id) {

        $query = (new SelectQuery(TSongs::_NAME))
            ->innerJoin(
                TPlaylistSongLinks::_NAME,
                TPlaylistSongLinks::_NAME.".".TPlaylistSongLinks::SONG_ID,
                TSongs::_NAME.".".TSongs::ID
            )
            ->where(TPlaylistSongLinks::PLAYLIST_ID, $playlist_id)
            ->where(TSongs::USER_ID, $me->getId())
            ->where(TSongs::FILE_ID . " IS NOT NULL")
            ->orderBy(TPlaylistSongLinks::ORDER_ID);

        $query->select(TSongs::defaultSelection());
        $query->select(TPlaylistSongLinks::LINK_ID);
        $query->select(TPlaylistSongLinks::ORDER_ID);

        ob_start("ob_gzhandler");

        $query->renderAllAsJson(function ($row) use ($playlist_id) {
            $artist_encoded = escape_url($row["album_artist"]);
            $album_encoded  = escape_url($row["track_album"]);
            $genre_encoded  = escape_url($row["track_genre"]);
            $row["playlist_url"] = "playlist/{$playlist_id}";
            $row["artist_url"] = "artist/{$artist_encoded}";
            $row["album_url"]  = "artist/{$artist_encoded}/{$album_encoded}";
            $row["genre_url"]  = "genre/{$genre_encoded}";
            return $row;
        });

    }
} 