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
use app\core\http\HttpGet;
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
use app\project\persistence\db\tables\TSongs;

class DoTracks implements RouteHandler {

    public function doGet(JsonResponse $response, Option $q, HttpGet $get, LoggedIn $me) {

        $artist  = $get->get("artist");
        $album   = $get->get("album");
        $genre   = $get->get("genre");

        $order_field = $get->getOrElse("sort", "auto");

        $filter = $q->map("trim")->reject("");

        $query = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, $me->getId())
            ->where(TSongs::FILE_ID . " IS NOT NULL");

        $query->select(TSongs::defaultSelection());

        Context::contextify($query);

        if ($artist->nonEmpty()) {
            $query->where(TSongs::A_ARTIST, $artist->get());
        }

        if ($album->nonEmpty()) {
            $query->where(TSongs::T_ALBUM, $album->get());
        }

        if ($genre->nonEmpty()) {
            $query->where(TSongs::T_GENRE, $genre->get());
        }

        if ($filter->nonEmpty()) {

            $query  ->where(TSongs::FTS_ANY . " @@ plainto_tsquery(?)", [$filter->get()]);

        } else {

            switch ($order_field) {
                case 'upload':
                    $query  ->orderBy(TSongs::C_DATE . " DESC");
                    break;
                default:
                    $query  ->orderBy(TSongs::A_ARTIST)
                            ->orderBy(TSongs::T_YEAR . " DESC")
                            ->orderBy(TSongs::T_ALBUM)
                            ->orderBy(TSongs::DISC)
                            ->orderBy(TSongs::T_NUMBER)
                            ->orderBy(TSongs::ID);
            }


        }

        $catalog = $query->fetchAll(null, function ($row) {
            $artist_encoded = urlencode($row["album_artist"]);
            $album_encoded  = urlencode($row["track_album"]);
            $row["artist_url"] = "#/tracks/grouped?artist={$artist_encoded}";
            $row["album_url"]  = "#/tracks/album?artist={$artist_encoded}&album={$album_encoded}";
            return $row;
        });

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 