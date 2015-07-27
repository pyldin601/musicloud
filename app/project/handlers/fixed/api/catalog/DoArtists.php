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
            ->innerJoin(MetadataTable::TABLE_NAME, MetadataTable::ARTIST_ID_FULL, MetaArtistsTable::ID_FULL)
            ->select(MetaArtistsTable::ID_FULL)
            ->select(MetaArtistsTable::ARTIST_FULL)
            ->select(MetadataTable::COVER_FILE_ID_FULL)
            ->selectCount(MetadataTable::ID_FULL, "tracks_count")
            ->selectCountDistinct(MetadataTable::ALBUM_ID_FULL, "albums_count")
            ->addGroupBy(MetaArtistsTable::ID_FULL)
            ->having("COUNT(".MetadataTable::ID_FULL.") > 0")
            ->where(MetaArtistsTable::USER_ID_FULL, $me->getId());

        $query->orderBy(MetaArtistsTable::ARTIST_FULL);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->match(MetaArtistsTable::ARTIST_FULL, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "artists" => $catalog
        ]);

    }
} 