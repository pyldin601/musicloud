<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 05.08.2015
 * Time: 11:09
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;
use app\project\models\single\LoggedIn;
use app\project\models\tracklist\Songs;

class DoCron implements RouteHandler {
    public function doGet(LoggedIn $me) {
        if ($me->getId() === 0) {
            Songs::wipeOldPreviews();
        }
    }
} 