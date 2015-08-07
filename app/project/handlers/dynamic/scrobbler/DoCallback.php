<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 07.08.2015
 * Time: 12:16
 */

namespace app\project\handlers\dynamic\scrobbler;


use app\core\router\RouteHandler;
use app\libs\AudioScrobbler;

class DoCallback implements RouteHandler {
    public function doGet($token) {
        $scrobbler = new AudioScrobbler();
        $scrobbler->login($token);
    }
} 