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

        $stats   = (new SelectQuery(TSongs::_NAME))
            ->selectCount(TSongs::ID, "tracks_count")
            ->selectCountDistinct(TSongs::A_ARTIST, "artists_count")
            ->selectCountDistinct(TSongs::T_ALBUM, "albums_count")
            ->selectCountDistinct(TSongs::T_GENRE, "genres_count")
            ->where(TSongs::USER_ID, $me->getId())
            ->fetchOneRow()->get();

        $response->write([
            "email" => $me->getEmail(),
            "name" => $me->getName(),
            "id" => $me->getId(),
            "stats" => $stats
        ]);

    }
} 