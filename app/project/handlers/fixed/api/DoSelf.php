<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 16:42
 */

namespace app\project\handlers\fixed\api;


use app\core\cache\RedisCache;
use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;

class DoSelf implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {

        $stats_cached = RedisCache::getMy("stats");

        if ($stats_cached->isEmpty()) {

            $stats = (new SelectQuery(MetadataTable::TABLE_NAME))
                ->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID, MetadataTable::ID)
                ->select("COUNT(DISTINCT " . MetadataTable::ALBUM_ARTIST . ") AS artists_count")
                ->select("COUNT(DISTINCT " . MetadataTable::ALBUM . ") AS albums_count")
                ->select("COUNT(DISTINCT " . MetadataTable::GENRE . ") AS genres_count")
                ->select("COUNT(" . MetadataTable::TABLE_NAME . "." . MetadataTable::ID . ") AS tracks_count")
                ->where(AudiosTable::TABLE_NAME . "." . AudiosTable::USER_ID, $me->getId())
                ->fetchOneRow()->get();

            RedisCache::putMy("stats", $stats);

        } else {

            $stats = $stats_cached->get();

        }

        $response->write([
            "email" => $me->getEmail(),
            "id" => $me->getId(),
            "stats" => $stats
        ]);
    }
} 