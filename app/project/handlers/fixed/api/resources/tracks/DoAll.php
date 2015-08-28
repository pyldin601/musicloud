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
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\TSongs;

class DoAll implements RouteHandler {
    public function doGet(LoggedIn $me) {
        ob_start("ob_gzhandler");
        header("Content-Type: application/x-javascript");

        (new SelectQuery(TSongs::_NAME))
            ->select(TSongs::ID, TSongs::FILE_ID, TSongs::BITRATE, TSongs::LENGTH, TSongs::T_TITLE,
                TSongs::T_ALBUM, TSongs::T_GENRE, TSongs::T_NUMBER, TSongs::T_COMMENT, TSongs::T_YEAR,
                TSongs::T_RATING, TSongs::IS_FAV, TSongs::IS_COMP, TSongs::DISC, TSongs::A_ARTIST,
                TSongs::T_PLAYED, TSongs::T_SKIPPED, TSongs::C_DATE, TSongs::LP_DATE,
                TSongs::C_SMALL_ID, TSongs::C_BIG_ID, TSongs::C_MID_ID, TSongs::FORMAT
            )
            ->where(TSongs::FILE_ID . " IS NOT NULL")
            ->where(TSongs::USER_ID, $me->getId())
            ->eachRow(function ($row) {
                echo 'this.insert(' . json_encode($row) . ');' . PHP_EOL;
            });
    }
} 