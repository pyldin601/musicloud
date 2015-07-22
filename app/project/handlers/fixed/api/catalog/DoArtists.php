<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 12:18
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Mapper;
use app\lang\option\Option;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\MetadataTable;

class DoArtists implements RouteHandler {
    public function doGet(JsonResponse $response, Option $o, Option $l, Option $q, LoggedIn $me) {

        $query = (new SelectQuery(MetadataTable::TABLE_NAME));

        $query->select(MetadataTable::ALBUM_ARTIST);
        $query->select("COUNT(distinct ".MetadataTable::ALBUM.") as albums");
        $query->select("COUNT(distinct ".MetadataTable::ID.") as tracks");

        $query->addGroupBy(MetadataTable::ALBUM_ARTIST);

        if ($o->nonEmpty()) {       $query->offset($o->get()); }
        if ($l->nonEmpty()) {        $query->limit($l->get()); }
        if ($q->nonEmpty() && strlen($q->get()) > 0) {
            $query->where("MATCH(".MetadataTable::ALBUM_ARTIST.") AGAINST(? IN BOOLEAN MODE)",
                array($q->map(Mapper::fulltext())->get()));
        }

        $catalog = $query->fetchAll();

        $response->write([
            "query" => $q->map(Mapper::fulltext())->get(),
            "artists" => $catalog
        ]);

    }
} 