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
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\TSongs;

class DoAlbums implements RouteHandler {
    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, $me->getId())
            ->select(TSongs::A_ARTIST)
            ->select(TSongs::T_ALBUM)
            ->select(TSongs::C_BIG_ID, TSongs::C_MID_ID, TSongs::C_SMALL_ID);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->match(TSongs::T_ALBUM, $filter->get());
        }

        $query->addGroupBy(TSongs::A_ARTIST);
        $query->addGroupBy(TSongs::T_ALBUM);

        $catalog = $query->fetchAll();

        $response->write([
            "albums" => $catalog
        ]);

    }
} 