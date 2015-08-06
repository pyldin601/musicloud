<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 10:16
 */

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\tracklist\Song;

class DoWavePeaks implements RouteHandler {
    public function doGet($id) {
        $tm = new Song($id);
        $peaks = $tm->getPeaks();
        header("Content-Type: application/json");
        ob_start("ob_gzhandler");
        echo $peaks;
    }
} 