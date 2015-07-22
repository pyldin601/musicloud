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
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;

class DoSelf implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {

        $stats = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID, MetadataTable::ID)
            ->select("COUNT(DISTINCT ".MetadataTable::ALBUM_ARTIST.") AS artist_count")
            ->select("COUNT(DISTINCT ".MetadataTable::ALBUM.") AS album_count")
            ->select("COUNT(DISTINCT ".MetadataTable::GENRE.") AS genre_count")
            ->select("COUNT(".MetadataTable::TABLE_NAME.".".MetadataTable::ID.") AS track_count")
            ->where(AudiosTable::TABLE_NAME.".".AudiosTable::USER_ID, $me->getId())
            ->fetchOneRow()->get();

        $response->write([
            "email" => $me->getEmail(),
            "id" => $me->getId(),
            "stats" => $stats
        ]);
    }
} 