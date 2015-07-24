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
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;

class DoGenres implements RouteHandler {
    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(MetaGenresTable::TABLE_NAME))
            ->select(MetaGenresTable::GENRE_FULL)
            ->select(MetaGenresTable::ID_FULL);

        CatalogTools::filterGenres($query);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->match(MetaGenresTable::GENRE_FULL, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "genres" => $catalog
        ]);
    }
} 