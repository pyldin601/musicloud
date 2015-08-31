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

        ob_start("ob_gzhandler", 8192);

        set_time_limit(0);

        (new SelectQuery(TSongs::_NAME))
            ->select(TSongs::$columns)
            ->where(TSongs::FILE_ID . " IS NOT NULL")
            ->where(TSongs::USER_ID, $me->getId())
            ->writeCSV();

    }


} 