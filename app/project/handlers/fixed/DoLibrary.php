<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 17.07.2015
 * Time: 9:42
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;
use app\core\view\TinyView;

class DoLibrary implements RouteHandler {
    public function doGet() {
        TinyView::show("library.tmpl");
    }
} 