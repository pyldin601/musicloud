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

class DoSelf implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {

        $artists = count(new SelectQuery(MetaArtistsTable::TABLE_NAME, MetaArtistsTable::USER_ID_FULL, $me->getId()));
        $albums  = count(new SelectQuery(MetaAlbumsTable::TABLE_NAME,  MetaAlbumsTable::USER_ID_FULL,  $me->getId()));
        $genres  = count(new SelectQuery(MetaGenresTable::TABLE_NAME,  MetaGenresTable::USER_ID_FULL,  $me->getId()));
        $tracks  = count(new SelectQuery(MetadataTable::TABLE_NAME,    MetadataTable::USER_ID_FULL,    $me->getId()));

        $response->write([
            "email" => $me->getEmail(),
            "id" => $me->getId(),
            "stats" => [
                "artists_count" => $artists,
                "albums_count" => $albums,
                "genres_count" => $genres,
                "tracks_count" => $tracks
            ]
        ]);

    }
} 