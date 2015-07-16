<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 17:00
 */

namespace app\project\handlers\fixed\api;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\models\UsersModel;

class DoLogout implements  RouteHandler {
    public function doPost(JsonResponse $response) {
        UsersModel::logout();
    }
} 