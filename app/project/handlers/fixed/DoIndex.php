<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 9:38
 */

namespace app\project\handlers\fixed;


use app\core\db\RDL;
use app\core\router\RouteHandler;

class DoIndex implements  RouteHandler {
    public function doGet(RDL $rdl) {
        echo $rdl->fetchOneColumn("SELECT NOW()");
    }
}