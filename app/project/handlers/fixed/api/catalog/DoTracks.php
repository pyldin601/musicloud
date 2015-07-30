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
use app\project\CatalogTools;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracks implements RouteHandler {

    public function doGet(JsonResponse $response, Option $q,
                          Option $artist_id, Option $album_id, Option $genre_id, LoggedIn $me) {

        $art_id = $artist_id->toInt();
        $alb_id = $album_id->toInt();
        $gen_id = $genre_id->toInt();

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->joinUsing(AudiosTable::TABLE_NAME, AudiosTable::ID)
            ->joinUsing(StatsTable::TABLE_NAME, StatsTable::ID)
            ->joinUsing(CoversTable::TABLE_NAME, CoversTable::ID)

            ->innerJoin(MetaAlbumsTable::TABLE_NAME,  MetaAlbumsTable::ID_FULL,  MetadataTable::ALBUM_ID_FULL)
            ->innerJoin(MetaArtistsTable::TABLE_NAME, MetaArtistsTable::ID_FULL, MetadataTable::ARTIST_ID_FULL)
            ->innerJoin(MetaGenresTable::TABLE_NAME,  MetaGenresTable::ID_FULL,  MetadataTable::GENRE_ID_FULL)

            ->where(MetadataTable::USER_ID_FULL, $me->getId())

            ->select(MetaAlbumsTable::ALBUM_FULL)
            ->selectAlias(MetaArtistsTable::ARTIST_FULL, "album_artist")
            ->select(MetaGenresTable::GENRE_FULL)

            ->selectAlias(MetaAlbumsTable::ID_FULL, "group_id")

            ->orderBy(MetaArtistsTable::ARTIST_FULL)
            ->orderBy(MetadataTable::DATE_FULL." DESC")
            ->orderBy(MetaAlbumsTable::ALBUM_FULL)
            ->orderBy(MetadataTable::TRACK_NUMBER);

        CatalogTools::commonSelectors($query);

        Context::contextify($query);

        if ($art_id->nonEmpty()) {
            $query->where(MetaArtistsTable::ID_FULL, $art_id->get());
        }

        if ($alb_id->nonEmpty()) {
            $query->where(MetaAlbumsTable::ID_FULL, $alb_id->get());
        }

        if ($gen_id->nonEmpty()) {
            $query->where(MetaGenresTable::ID_FULL, $gen_id->get());
        }

        if ($filter->nonEmpty()) {
            $query->match(MetadataTable::TITLE_FULL, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 