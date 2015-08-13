<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 16.07.2015
 * Time: 9:38
 */

namespace app\project\handlers\fixed;


use app\core\http\HttpStatusCodes;
use app\core\router\RouteHandler;
use app\core\view\TinyView;
use app\project\models\single\LoggedIn;

class DoIndex implements RouteHandler {
    public function doGet() {
//        $logged_in = LoggedIn::isLoggedIn();
        $logged_in = false;
        if ($logged_in) {
            http_response_code(HttpStatusCodes::HTTP_FOUND);
            header("Location: /library/");
        } else {
            TinyView::show("login.tmpl");
        }
    }
}