<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 01.08.15
 * Time: 00:14
 */

namespace app\project\handlers\fixed;


use app\core\etc\Settings;
use app\core\router\RouteHandler;

class DoTest implements RouteHandler {
    public function doGet() {
        echo resource(Settings::class)->get("command_templates", "make_covers");
    }
}

