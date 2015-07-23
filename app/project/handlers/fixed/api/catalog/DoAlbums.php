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
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;

class DoAlbums implements RouteHandler {
    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $query = (new SelectQuery(MetadataTable::TABLE_NAME));

        $query->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID, MetadataTable::ID);
        $query->where(AudiosTable::USER_ID, $me->getId());

        $query->select(MetadataTable::ALBUM_ARTIST);
        $query->select(MetadataTable::ALBUM);
        $query->select("COUNT(distinct ".MetadataTable::TABLE_NAME.".".MetadataTable::ID.") as tracks");

        $query->addGroupBy(MetadataTable::ALBUM);
        $query->addGroupBy(MetadataTable::ALBUM_ARTIST);

        Context::contextify($query);

        CatalogTools::commonSelectAlbum($query);

        if ($q->nonEmpty() && strlen($q->get()) > 0) {
            $query->where("MATCH(".MetadataTable::ALBUM.") AGAINST(? IN BOOLEAN MODE)",
                array($q->map(Mapper::fulltext())->get()));
        }

        $catalog = $query->fetchAll();

        $response->write([
            "albums" => $catalog
        ]);

    }
} 