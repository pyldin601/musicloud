<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:03
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Mapper;
use app\lang\option\Option;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracks implements RouteHandler {

    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $query = (new SelectQuery(MetadataTable::TABLE_NAME));

        $query->innerJoin(AudiosTable::TABLE_NAME, AudiosTable::ID, MetadataTable::ID);
        $query->innerJoin(StatsTable::TABLE_NAME, StatsTable::ID, MetadataTable::ID);
        $query->where(AudiosTable::USER_ID, $me->getId());

        $query->select(
            MetadataTable::ALBUM,
            MetadataTable::TITLE,
            MetadataTable::ARTIST,
            MetadataTable::ALBUM_ARTIST,
            MetadataTable::BITRATE,
            MetadataTable::DATE,
            MetadataTable::DURATION,
            MetadataTable::GENRE,
            MetadataTable::TABLE_NAME.".".MetadataTable::ID,
            MetadataTable::RATING,
            MetadataTable::TRACK_NUMBER,
            MetadataTable::COVER_FILE_ID,
            AudiosTable::CREATED_DATE,
            StatsTable::LAST_PLAYED_DATE,
            StatsTable::PLAYBACKS,
            StatsTable::SKIPS
        );

        Context::contextify($query);

        $query->orderBy(MetadataTable::ALBUM_ARTIST);
        $query->orderBy(MetadataTable::ALBUM);
        $query->orderBy(MetadataTable::TRACK_NUMBER);

        if ($q->nonEmpty() && strlen($q->get()) > 0) {
            $cols = implode(",", [
                MetadataTable::ALBUM_ARTIST,
                MetadataTable::ARTIST,
                MetadataTable::TITLE,
                MetadataTable::ALBUM,
                MetadataTable::GENRE
            ]);
            $query->where("MATCH({$cols}) AGAINST(? IN BOOLEAN MODE)",
                array($q->map(Mapper::fulltext())->get()));
        }

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 