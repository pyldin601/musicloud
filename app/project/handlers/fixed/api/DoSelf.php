<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 16:42
 */

namespace app\project\handlers\fixed\api;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\TSongs;

class DoSelf implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {

        $songs_count = (new SelectQuery(TSongs::_NAME))
            ->select("COUNT(".TSongs::ID.")")
            ->where(TSongs::USER_ID, $me->getId())
            ->fetchColumn()->get();

        $response->write([
            "email" => $me->getEmail(),
            "name" => $me->getName(),
            "id" => $me->getId(),
            "stats" => [
                "tracks_count" => $songs_count,
                "artists_count" => 0,
                "albums_count" => 0,
                "genres_count" => 0,
                "compilations_count" => 0
            ]
        ]);

    }
} 