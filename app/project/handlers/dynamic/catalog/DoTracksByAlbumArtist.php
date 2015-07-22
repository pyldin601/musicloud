<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:33
 */

namespace app\project\handlers\dynamic\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Option;
use app\project\CatalogTools;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracksByAlbumArtist implements RouteHandler {

    public function doGet(JsonResponse $response, $artist, LoggedIn $me) {

        $query = (new SelectQuery(MetadataTable::TABLE_NAME));

        $query->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID, MetadataTable::ID);
        $query->innerJoin(StatsTable::TABLE_NAME, StatsTable::ID, MetadataTable::ID);

        $query->where(AudiosTable::USER_ID, $me->getId());

        CatalogTools::commonSelectors($query);

        Context::contextify($query);

        $query->orderBy(MetadataTable::ALBUM_ARTIST);
        $query->orderBy(MetadataTable::ALBUM);
        $query->orderBy(MetadataTable::TRACK_NUMBER);

        $query->where(MetadataTable::ALBUM_ARTIST, urldecode($artist));

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }

} 