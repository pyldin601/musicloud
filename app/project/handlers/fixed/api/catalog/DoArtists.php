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
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\TSongs;

class DoArtists implements RouteHandler {
    public function doGet(Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("");

        $query = (new SelectQuery(TSongs::_NAME))
            ->select(TSongs::A_ARTIST)
            ->where(TSongs::USER_ID, $me->getId())
            ->where(TSongs::A_ARTIST . " != ''")
            ->selectAlias("MIN(".TSongs::C_BIG_ID.")", TSongs::C_BIG_ID)
            ->selectAlias("MIN(".TSongs::C_MID_ID.")", TSongs::C_MID_ID)
            ->selectAlias("MIN(".TSongs::C_SMALL_ID.")", TSongs::C_SMALL_ID);

        $query->where(TSongs::IS_COMP, "0");

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->where(TSongs::FTS_ARTIST . " @@ plainto_tsquery(?)", [$filter->get()]);
        }

        $query->addGroupBy(TSongs::A_ARTIST);

        $query->orderBy(TSongs::A_ARTIST);

        ob_start("ob_gzhandler");

        $query->renderAllAsJson(function ($row) {
            $artist_encoded = escape_url($row["album_artist"]);
            $row["artist_url"] = "artist/{$artist_encoded}";
            return $row;
        });

    }
} 