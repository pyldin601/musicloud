<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 04.08.15
 * Time: 20:26
 */

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\project\models\tracklist\Track;

class DoGetPreview implements RouteHandler {
    public function doGet($id) {
        $track = new Track($id);
        $track->preview();
    }
}