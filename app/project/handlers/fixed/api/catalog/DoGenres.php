<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 14:51
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Filter;
use app\lang\option\Mapper;
use app\lang\option\Option;
use app\project\CatalogTools;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\TSongs;

class DoGenres implements RouteHandler {
    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("");

        $query = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, $me->getId())
            ->select(TSongs::T_GENRE)
            ->selectAlias("MIN(".TSongs::C_BIG_ID.")", TSongs::C_BIG_ID)
            ->selectAlias("MIN(".TSongs::C_MID_ID.")", TSongs::C_MID_ID)
            ->selectAlias("MIN(".TSongs::C_SMALL_ID.")", TSongs::C_SMALL_ID);


        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->where(TSongs::FTS_GENRE . " @@ plainto_tsquery(?)", [$filter->get()]);
        }

        $query->addGroupBy(TSongs::T_GENRE);

        $query->orderBy(TSongs::T_GENRE);

        Context::contextify($query);

        $catalog = $query->fetchAll(null, function ($row) {
            $genre_encoded = urlencode($row["track_genre"]);
            $row["genre_url"] = "#/tracks/grouped?genre={$genre_encoded}";
            return $row;
        });

        $response->write([
            "genres" => $catalog
        ]);
    }
} 