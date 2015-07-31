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

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(TSongs::_NAME." a"))
            ->where("a.".TSongs::USER_ID, $me->getId())
            ->select("DISTINCT a." . TSongs::T_GENRE)
            ->selectAlias("(SELECT ".TSongs::C_SMALL_ID." FROM ".TSongs::_NAME." WHERE ".TSongs::T_GENRE." = a.".TSongs::T_GENRE." LIMIT 1)", TSongs::C_SMALL_ID)
            ->selectAlias("(SELECT ".TSongs::C_MID_ID." FROM ".TSongs::_NAME." WHERE ".TSongs::T_GENRE." = a.".TSongs::T_GENRE." LIMIT 1)", TSongs::C_MID_ID)
            ->selectAlias("(SELECT ".TSongs::C_BIG_ID." FROM ".TSongs::_NAME." WHERE ".TSongs::T_GENRE." = a.".TSongs::T_GENRE." LIMIT 1)", TSongs::C_BIG_ID);


        Context::contextify($query);

//        $query->addGroupBy(TSongs::T_GENRE);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->match(TSongs::T_GENRE, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "genres" => $catalog
        ]);
    }
} 