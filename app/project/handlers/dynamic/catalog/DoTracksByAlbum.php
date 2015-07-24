<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 15:36
 */

namespace app\project\handlers\dynamic\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\CatalogTools;
use app\project\exceptions\AlbumArtistNotFoundException;
use app\project\exceptions\AlbumNotFoundException;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\MetaGenresTable;
use app\project\persistence\db\tables\StatsTable;

class DoTracksByAlbum implements RouteHandler {
    public function doGet(JsonResponse $response, $artist, $album, LoggedIn $me) {

        $artist_id = (new SelectQuery(MetaArtistsTable::TABLE_NAME))
            ->where(MetaArtistsTable::ARTIST_FULL, urldecode($artist))
            ->where(MetaArtistsTable::USER_ID_FULL, $me->getId())
            ->fetchOneColumn()->toInt()->getOrThrow(AlbumArtistNotFoundException::class);

        $album_id = (new SelectQuery(MetaAlbumsTable::TABLE_NAME))
            ->where(MetaAlbumsTable::ALBUM_FULL, urldecode($album))
            ->where(MetaAlbumsTable::ARTIST_ID_FULL, $artist_id)
            ->fetchOneColumn()->toInt()->getOrThrow(AlbumNotFoundException::class);

        $query = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->joinUsing(AudiosTable::TABLE_NAME, AudiosTable::ID)
            ->joinUsing(StatsTable::TABLE_NAME, StatsTable::ID)

            ->where(MetadataTable::ALBUM_ID_FULL, $album_id)

            ->selectAlias(sprintf("(SELECT %s FROM %s WHERE %s = %s)",
                MetaGenresTable::GENRE, MetaGenresTable::TABLE_NAME, MetaGenresTable::ID, MetadataTable::GENRE_ID
            ), "genre")

            ->orderBy(MetadataTable::TRACK_NUMBER);

        Context::contextify($query);

        CatalogTools::commonSelectors($query);

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 