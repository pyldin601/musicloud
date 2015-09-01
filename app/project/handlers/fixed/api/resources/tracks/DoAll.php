<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.08.2015
 * Time: 10:03
 */

namespace app\project\handlers\fixed\api\resources\tracks;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\db\tables\TSongsLog;

class DoAll implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {

        ob_start("ob_gzhandler", 8192);

        set_time_limit(0);

        $last_token = (new SelectQuery(TSongsLog::_NAME))
            ->select(TSongsLog::ID)
            ->fetchColumn()
            ->toInt()
            ->getOrElse(0);

        $tracks = (new SelectQuery(TSongs::_NAME))
            ->select(TSongs::$columns)
            ->where(TSongs::FILE_ID . " IS NOT NULL")
            ->where(TSongs::USER_ID, $me->getId())
            ->fetchAll();

        $response->write(array(
            "last_token" => $last_token,
//            "tracks" => $tracks
        ));

    }


} 