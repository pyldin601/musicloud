<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:03
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Mapper;
use app\lang\option\Option;
use app\project\CatalogTools;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracks implements RouteHandler {

    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID_FULL, MetadataTable::ID_FULL)
            ->innerJoin(StatsTable::TABLE_NAME, StatsTable::ID_FULL, MetadataTable::ID_FULL)

            ->where(MetadataTable::USER_ID_FULL, $me->getId())

            ->orderBy(MetadataTable::ALBUM_ARTIST)
            ->orderBy(MetadataTable::ALBUM)
            ->orderBy(MetadataTable::TRACK_NUMBER);

        CatalogTools::commonSelectors($query);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $cols = implode(",", [
                MetadataTable::ALBUM_ARTIST,
                MetadataTable::ARTIST,
                MetadataTable::TITLE,
                MetadataTable::ALBUM,
                MetadataTable::GENRE
            ]);
            $query->match($cols, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 