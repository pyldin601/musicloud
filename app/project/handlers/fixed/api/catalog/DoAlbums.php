<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 13:52
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Mapper;
use app\lang\option\Option;
use app\project\CatalogTools;
use app\project\libs\Metadata;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;

class DoAlbums implements RouteHandler {
    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(MetaAlbumsTable::TABLE_NAME))
            ->innerJoin(MetaArtistsTable::TABLE_NAME, MetaArtistsTable::ID_FULL, MetaAlbumsTable::ARTIST_ID_FULL)
            ->innerJoin(MetadataTable::TABLE_NAME, MetadataTable::ALBUM_ID_FULL, MetaAlbumsTable::ID_FULL)
            ->select(MetaAlbumsTable::ID_FULL)
            ->select(MetaAlbumsTable::ALBUM_FULL)
            ->select(MetaArtistsTable::ARTIST_FULL)
            ->select(MetadataTable::COVER_FILE_ID_FULL)
            ->selectCount(MetadataTable::ID_FULL, "tracks_count")
            ->selectAlias(MetaArtistsTable::ID_FULL, "artist_id")
            ->where(MetaAlbumsTable::USER_ID_FULL, $me->getId())
            ->having("COUNT(".MetadataTable::ID_FULL.") > 0")
            ->addGroupBy(MetaAlbumsTable::ID_FULL);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->match(MetaAlbumsTable::ALBUM_FULL, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "albums" => $catalog
        ]);

    }
} 