<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 02.07.15
 * Time: 22:33
 */

namespace app\project\handlers\dynamic;


use app\core\router\RouteHandler;

class DoRegistrationPage implements RouteHandler {
    public function doGet() {
        echo "Registration";
    }
}