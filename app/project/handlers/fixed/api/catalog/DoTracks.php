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

        $artist  = $get->get("artist")->reject("");
        $album   = $get->get("album")->reject("");
        $genre   = $get->get("genre")->reject("");
        $shuffle = $get->get("shuffle_id")->toInt();

        $filter = $q->reject("")->map(Mapper::fulltext());

        $query = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, $me->getId());

        if ($shuffle->isEmpty()) {
            $query  ->orderBy(TSongs::A_ARTIST)
                    ->orderBy(TSongs::T_YEAR)
                    ->orderBy(TSongs::T_ALBUM)
                    ->orderBy(TSongs::DISC)
                    ->orderBy(TSongs::T_NUMBER);
        } else {
            $query  ->orderBy("RAND(".$shuffle->get().")");
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
            $query->match(implode(",",[
                TSongs::A_ARTIST,
                TSongs::T_ARTIST,
                TSongs::T_ALBUM,
                TSongs::T_TITLE,
                TSongs::T_GENRE
            ]), $filter->get());
        }

        $catalog = $query->fetchAll();

        $response->write([
            "tracks" => $catalog
        ]);

    }
} 