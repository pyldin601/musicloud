<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 27.07.2015
 * Time: 14:20
 */

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\project\persistence\fs\FileServer;

class DoReadRawContent implements RouteHandler {
    public function doGet($id) {
//        FileServer::writeToClient($id);
    }
} 