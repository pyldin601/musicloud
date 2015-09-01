<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 01.09.2015
 * Time: 10:32
 */

namespace app\project\handlers\fixed\api\resources\tracks;


use app\core\db\builder\SelectQuery;
use app\core\router\RouteHandler;
use app\lang\option\Option;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\db\tables\TSongsLog;

class DoUpdate implements RouteHandler {
    public function doGet(LoggedIn $me, Option $sync_token) {
        $token = $sync_token->getOrElse(0);
        $new_tracks = (new SelectQuery(TSongs::_NAME))
            ->select(TSongs::getFullColumnNames())
            ->innerJoin(TSongsLog::_NAME, TSongs::_NAME . "." . TSongs::ID, TSongsLog::SONG_ID)
            ->where(TSongsLog::_NAME . "." . TSongsLog::USER_ID, $me->getId())
            ->where(TSongsLog::_NAME . "." . TSongsLog::ACTION, TSongsLog::ACTION_ADD)
            ->where(TSongsLog::_NAME . "." . TSongsLog::ID . " > ?", array($token))
            ->fetchAll();
//        $updated_tracks = (new SelectQuery(TSongs::_NAME))
//            ->select(TSongs::getFullColumnNames())
//            ->innerJoin(TSongsLog::_NAME, TSongs::_NAME . "." . TSongs::ID, TSongsLog::SONG_ID)
//            ->where(TSongsLog::_NAME . "." . TSongsLog::USER_ID, $me->getId())
//            ->where(TSongsLog::_NAME . "." . TSongsLog::ACTION, TSongsLog::ACTION_UPDATE)
//            ->where(TSongsLog::_NAME . "." . TSongsLog::ID . " > ?", array($token))
//            ->fetchAll();
//        echo json_encode(array(
//            "new" => $new_tracks,
//            "updated" => $updated_tracks
//        ));
    }
} 