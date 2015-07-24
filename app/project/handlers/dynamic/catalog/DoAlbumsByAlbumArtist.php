<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 23.07.2015
 * Time: 9:54
 */

namespace app\project\handlers\dynamic\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\exceptions\AlbumArtistNotFoundException;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;


class DoAlbumsByAlbumArtist implements RouteHandler {
    public function doGet(JsonResponse $response, $artist, LoggedIn $me) {

        $artist_id = (new SelectQuery(MetaArtistsTable::TABLE_NAME))
            ->where(MetaArtistsTable::ARTIST_FULL, urldecode($artist))
            ->where(MetaArtistsTable::USER_ID_FULL, $me->getId())
            ->fetchOneColumn()->toInt()->getOrThrow(AlbumArtistNotFoundException::class);

        $query = (new SelectQuery(MetaAlbumsTable::TABLE_NAME))
            ->innerJoin(MetadataTable::TABLE_NAME, MetadataTable::ALBUM_ID_FULL, MetaAlbumsTable::ID_FULL)
            ->where(MetaAlbumsTable::ARTIST_ID_FULL, $artist_id)
            ->selectCount(MetadataTable::ID_FULL, "tracks_count")
            ->select(MetaAlbumsTable::ALBUM_FULL)
            ->selectAlias(MetaAlbumsTable::ID_FULL, "album_id")
            ->select(MetadataTable::DATE_FULL)
            ->select(MetadataTable::COVER_FILE_ID_FULL)
            ->addGroupBy(MetaAlbumsTable::ID_FULL);


        Context::contextify($query);

        $catalog = $query->fetchAll();

        $response->write([
            "albums" => $catalog
        ]);

    }
} 