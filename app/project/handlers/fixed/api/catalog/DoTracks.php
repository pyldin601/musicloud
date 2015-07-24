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
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracks implements RouteHandler {

    public function doGet(JsonResponse $response, Option $q, LoggedIn $me) {

        $filter = $q->map("trim")->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->joinUsing(AudiosTable::TABLE_NAME, AudiosTable::ID)
            ->joinUsing(StatsTable::TABLE_NAME, StatsTable::ID)

            ->where(MetadataTable::USER_ID_FULL, $me->getId())

            ->selectAlias(sprintf("(SELECT %s FROM %s WHERE %s = %s)",
                MetaAlbumsTable::ALBUM, MetaAlbumsTable::TABLE_NAME, MetaAlbumsTable::ID, MetadataTable::ALBUM_ID
            ), "album")
            ->selectAlias(sprintf("(SELECT %s FROM %s WHERE %s = %s)",
                MetaArtistsTable::ARTIST, MetaArtistsTable::TABLE_NAME, MetaArtistsTable::ID, MetadataTable::ARTIST_ID
            ), "album_artist")
            ->selectAlias(sprintf("(SELECT %s FROM %s WHERE %s = %s)",
                MetaGenresTable::GENRE, MetaGenresTable::TABLE_NAME, MetaGenresTable::ID, MetadataTable::GENRE_ID
            ), "genre")

            ->orderBy(MetadataTable::ARTIST_ID_FULL)
            ->orderBy(MetadataTable::ALBUM_ID_FULL)
            ->orderBy(MetadataTable::TRACK_NUMBER);

        CatalogTools::commonSelectors($query);

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $cols = implode(",", [
                MetadataTable::ALBUM_ARTIST,
                MetadataTable::ARTIST,
                MetadataTable::TITLE,
                MetadataTable::ALBUM,
                MetadataTable::GENRE
            ]);
            $query->match($cols, $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 