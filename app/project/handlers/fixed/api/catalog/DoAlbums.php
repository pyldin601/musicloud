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
            ->select(sprintf("(SELECT %s FROM %s WHERE %s = %s AND %s IS NOT NULL LIMIT 1) AS cover_file_id",
                MetadataTable::COVER_FILE_ID_FULL, MetadataTable::TABLE_NAME,
                MetadataTable::ALBUM_ID_FULL, MetaAlbumsTable::ID_FULL, MetadataTable::COVER_FILE_ID_FULL))
            ->select(MetaAlbumsTable::ALBUM_FULL)
            ->select(MetaArtistsTable::ARTIST_FULL);

        Context::contextify($query);

        CatalogTools::filterAlbums($query);

        if ($filter->nonEmpty()) {
            $query->match(MetaAlbumsTable::ALBUM_FULL, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "albums" => $catalog
        ]);

    }
} 