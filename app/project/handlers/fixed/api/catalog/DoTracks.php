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

    public function doGet(Option $q, HttpGet $get, LoggedIn $me, Option $compilations) {

        $artist  = $get->get("artist")->map("urldecode");
        $album   = $get->get("album")->map("urldecode");
        $genre   = $get->get("genre")->map("urldecode");

        $order_field = $get->getOrElse("sort", "auto");

        $filter = $q->map("trim")->reject("");

        $query = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, $me->getId())
            ->where(TSongs::FILE_ID . " IS NOT NULL");

        $query->select(TSongs::defaultSelection());

        if ($compilations->nonEmpty()) {
            $query->where(TSongs::IS_COMP, $compilations->get());
        }

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

            if (strpos($filter->get(), ":") !== false) {
                list ($key, $value) = explode(":", $filter->get(), 2);
                switch ($key) {
                    case "id":
                        $query->where(TSongs::ID, $value);
                        break;
                }
            } else {
                $query  ->where(TSongs::FTS_ANY . " @@ plainto_tsquery(?)", [$filter->get()]);
            }


        } else {

            switch ($order_field) {
                case 'upload':
                    $query  ->orderBy(TSongs::C_DATE . " DESC")
                            ->orderBy(TSongs::ID);
                    break;
                default:
                    $query  ->orderBy(TSongs::A_ARTIST)
                            ->orderBy(TSongs::T_ALBUM)
                            ->orderBy(TSongs::DISC)
                            ->orderBy(TSongs::T_NUMBER)
                            ->orderBy(TSongs::ID);
            }


        }

        ob_start("ob_gzhandler");

        $query->renderAllAsJson(function ($row) {
            $artist_encoded = escape_url($row["album_artist"]);
            $album_encoded  = escape_url($row["track_album"]);
            $genre_encoded  = escape_url($row["track_genre"]);
            $row["artist_url"] = "artist/{$artist_encoded}";
            $row["album_url"]  = "artist/{$artist_encoded}/{$album_encoded}";
            $row["genre_url"]  = "genre/{$genre_encoded}";
            return $row;
        });

    }
} 