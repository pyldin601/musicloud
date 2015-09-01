<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 01.09.2015
 * Time: 15:30
 */

namespace app\project\handlers\fixed\api\v2;


use app\core\http\HttpJson;
use app\core\router\RouteHandler;

class DoTracks implements RouteHandler {
    public function doPatch(HttpJson $json) {
        print_r($json->data);
    }
} 