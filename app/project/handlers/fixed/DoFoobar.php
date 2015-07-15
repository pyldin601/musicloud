<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.07.2015
 * Time: 17:48
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;

class DoFoobar implements RouteHandler {
    public function doGet() {
        throw new \Exception("Bazaaaa!");
    }
}