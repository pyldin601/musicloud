<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 23.07.2015
 * Time: 9:54
 */

namespace app\project\handlers\dynamic\catalog;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\CatalogTools;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;

class DoAlbumsByAlbumArtist implements RouteHandler {
    public function doGet(JsonResponse $response, $artist, LoggedIn $me) {

        $query = new SelectQuery(MetadataTable::TABLE_NAME);

        $query->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID, MetadataTable::ID);

        $query->where(AudiosTable::USER_ID, $me->getId());

        CatalogTools::commonSelectAlbum($query);

        $query->addGroupBy(MetadataTable::ALBUM_ARTIST);
        $query->addGroupBy(MetadataTable::ALBUM);

        $query->orderBy(MetadataTable::DATE . " DESC");
        $query->orderBy(MetadataTable::ALBUM);

        $query->where(MetadataTable::ALBUM_ARTIST, urldecode($artist));

        $catalog = $query->fetchAll();

        $response->write([
            "albums" => $catalog
        ]);

    }
} 