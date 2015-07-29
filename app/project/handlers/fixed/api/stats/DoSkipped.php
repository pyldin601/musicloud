<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 29.07.15
 * Time: 21:14
 */

namespace app\project\handlers\fixed\api\stats;


use app\core\router\RouteHandler;
use app\project\models\tracklist\Track;

class DoSkipped implements RouteHandler {
    public function doPost($id) {
        $track = new Track($id);
        $track->incrementSkips();
    }
}