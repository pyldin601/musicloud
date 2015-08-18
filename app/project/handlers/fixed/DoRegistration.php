<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 18.08.2015
 * Time: 12:37
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;
use app\core\view\TinyView;

class DoRegistration implements RouteHandler {
    public function doGet() {
        TinyView::show("signup.tmpl");
    }
} 