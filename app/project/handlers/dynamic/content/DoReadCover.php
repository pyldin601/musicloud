<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 21.07.15
 * Time: 21:22
 */

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\project\models\tracklist\Track;

class DoReadCover implements RouteHandler {
    public function doGet($id) {
        $tm = new Track($id);
        $tm->cover();
    }
}