<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 13:52
 */

namespace app\project\handlers\fixed\api\catalog;


use app\core\db\builder\SelectQuery;
use app\core\etc\Context;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\lang\option\Mapper;
use app\lang\option\Option;
use app\project\CatalogTools;
use app\project\libs\Metadata;
use app\project\models\single\LoggedIn;
use app\project\models\tracklist\Songs;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetaAlbumsTable;
use app\project\persistence\db\tables\MetaArtistsTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\TSongs;

class DoAlbums implements RouteHandler {
    public function doGet(Option $q, LoggedIn $me, Option $compilations) {

        $filter = $q->reject("");

        $query = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, $me->getId())
            ->where(TSongs::T_ALBUM . " != ''")
            ->select(TSongs::A_ARTIST)
            ->select(TSongs::T_ALBUM)
            ->selectAlias("MIN(".TSongs::C_BIG_ID.")", TSongs::C_BIG_ID)
            ->selectAlias("MIN(".TSongs::C_MID_ID.")", TSongs::C_MID_ID)
            ->selectAlias("MIN(".TSongs::C_SMALL_ID.")", TSongs::C_SMALL_ID);

        if ($compilations->nonEmpty()) {
            $query->where(TSongs::IS_COMP, $compilations->get());
        }

        Context::contextify($query);

        if ($filter->nonEmpty()) {
            $query->where(TSongs::FTS_ALBUM . " @@ plainto_tsquery(?)", [$filter->get()]);
        }

        $query->addGroupBy(TSongs::A_ARTIST);
        $query->addGroupBy(TSongs::T_ALBUM);

        $query->orderBy(TSongs::T_ALBUM);

        ob_start("ob_gzhandler");

        header("Content-Type: application/json; charset=utf8");

        echo '{"albums":[';

        $query->eachRow(function ($row, $id) {
            $artist_encoded = escape_url($row["album_artist"]);
            $album_encoded  = escape_url($row["track_album"]);
            $row["artist_url"] = "artist/{$artist_encoded}";
            $row["album_url"]  = "artist/{$artist_encoded}/{$album_encoded}";
            if ($id != 0) {
                echo ",";
            }
            echo json_encode($row, JSON_UNESCAPED_UNICODE);
        });

        echo ']}';

    }
} 