<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:59
 */

namespace app\project\handlers\fixed\api;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\forms\LoginForm;
use app\project\models\Auth;

class DoLogin implements RouteHandler {
    public function doPost(JsonResponse $response, LoginForm $form) {
        Auth::login($form);
    }
} 