<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 12.08.2015
 * Time: 11:19
 */

namespace app\project\handlers\fixed\api\headers;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\TSongs;

class DoGenre implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me, $genre) {

        $artist_stats = (new SelectQuery(TSongs::_NAME))
            ->selectAlias("COUNT(DISTINCT ".TSongs::T_ALBUM.")", "albums_count")
            ->selectAlias("COUNT(".TSongs::ID.")", "tracks_count")
            ->selectAlias("SUM(".TSongs::LENGTH.")", "tracks_duration")
            ->selectAlias("MIN(".TSongs::C_BIG_ID.")", TSongs::C_BIG_ID)
            ->selectAlias("MIN(".TSongs::C_MID_ID.")", TSongs::C_MID_ID)
            ->selectAlias("MIN(".TSongs::C_SMALL_ID.")", TSongs::C_SMALL_ID)
            ->where(TSongs::USER_ID, $me->getId())
            ->where(TSongs::T_GENRE, $genre)
            ->fetchOneRow()->get();

        $artist_stats["genre"] = $genre;

        $response->write($artist_stats);

    }
} 