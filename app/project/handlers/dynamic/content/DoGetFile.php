<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 28.07.15
 * Time: 22:07
 */

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\project\persistence\fs\FileServer;

class DoGetFile implements RouteHandler {
    // todo: Use this method anywhere
    public function doGet($id) {
        FileServer::sendToClient($id);
    }
}