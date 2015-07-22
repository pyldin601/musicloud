<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 16:42
 */

namespace app\project\handlers\fixed\api;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;

class DoSelf implements RouteHandler {
    public function doGet(JsonResponse $response, LoggedIn $me) {
        $response->write([
            "email" => $me->getEmail(),
            "id" => $me->getId()
        ]);
    }
} 