<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 16.07.2015
 * Time: 9:38
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;
use app\project\models\single\LoggedIn;

class DoIndex implements RouteHandler {
    public function doGet(LoggedIn $user) {
        echo $user;
    }
}