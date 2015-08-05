<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 04.08.15
 * Time: 20:26
 */

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\project\models\tracklist\Song;

class DoGetPreview implements RouteHandler {
    public function doGet($id) {
        $track = new Song($id);
        $track->preview();
    }
}