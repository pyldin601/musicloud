<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:38
 */

namespace app\project\handlers\dynamic\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\CatalogTools;
use app\project\exceptions\GenreNotFoundException;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracksByGenre implements RouteHandler {
    public function doGet(JsonResponse $response, $genre, LoggedIn $me) {

        $genre_id = (new SelectQuery(MetaGenresTable::TABLE_NAME))
            ->where(MetaGenresTable::GENRE_FULL, urldecode($genre))
            ->where(MetaGenresTable::USER_ID_FULL, $me->getId())
            ->fetchOneColumn()->toInt()->getOrThrow(GenreNotFoundException::class);

        $query = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->joinUsing(AudiosTable::TABLE_NAME, AudiosTable::ID)
            ->joinUsing(StatsTable::TABLE_NAME, StatsTable::ID)

            ->innerJoin(MetaAlbumsTable::TABLE_NAME, MetaAlbumsTable::ID_FULL, MetadataTable::ALBUM_ID_FULL)
            ->innerJoin(MetaArtistsTable::TABLE_NAME, MetaAlbumsTable::ID_FULL, MetaAlbumsTable::ARTIST_ID_FULL)

            ->where(MetadataTable::GENRE_ID_FULL, $genre_id)

            ->select(MetaAlbumsTable::ALBUM_FULL)
            ->selectAlias(MetaArtistsTable::ARTIST_FULL, "album_artist")

            ->orderBy(MetadataTable::ARTIST_ID_FULL)
            ->orderBy(MetadataTable::DATE . " DESC")
            ->orderBy(MetadataTable::ALBUM_ID_FULL)
            ->orderBy(MetadataTable::TRACK_NUMBER);

        Context::contextify($query);

        CatalogTools::commonSelectors($query);

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
}