<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 17.08.2015
 * Time: 16:19
 */

namespace app\project\handlers\fixed\api\user;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\forms\RegistrationForm;
use app\project\models\Users;

class DoSignUp implements RouteHandler {
    public function doPost(JsonResponse $response, RegistrationForm $form) {
        Users::create($form);
    }
} 