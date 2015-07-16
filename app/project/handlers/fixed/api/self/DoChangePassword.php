<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 17:04
 */

namespace app\project\handlers\fixed\api\self;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\forms\PasswordForm;
use app\project\models\single\LoggedInUserModel;

class DoChangePassword implements RouteHandler {
    public function doPost(JsonResponse $response, PasswordForm $form, LoggedInUserModel $userModel) {
        $userModel->changePassword($form->getPassword());
    }
} 