<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 12:18
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
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;

class DoArtists implements RouteHandler {
    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(MetaArtistsTable::TABLE_NAME))

//            ->innerJoin(MetaAlbumsTable::TABLE_NAME, MetaAlbumsTable::ARTIST_ID_FULL, MetaArtistsTable::ID_FULL)
//            ->innerJoin(MetadataTable::TABLE_NAME, MetadataTable::ARTIST_ID_FULL, MetaArtistsTable::ID_FULL)
//            ->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID_FULL, MetadataTable::ID_FULL)

            ->select(MetaArtistsTable::ID_FULL)
            ->select(MetaArtistsTable::ARTIST_FULL);
//            ->select("COUNT(".MetadataTable::ID_FULL.") as tracks_count")
//            ->select("COUNT(DISTINCT ".MetaAlbumsTable::ID_FULL.") as albums_count")
//            ->addGroupBy(MetaArtistsTable::ARTIST_FULL);

        CatalogTools::filterArtist($query);

        Context::contextify($query);

        if ($q->nonEmpty()) {
            $query->match(MetaArtistsTable::ARTIST_FULL, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "artists" => $catalog
        ]);

    }
} 