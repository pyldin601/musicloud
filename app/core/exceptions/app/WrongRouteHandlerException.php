<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 9:49
 */

namespace app\core\exceptions\app;


use app\core\exceptions\ApplicationException;

class WrongRouteHandlerException extends ApplicationException {
    public function __construct() {
        parent::__construct("Wrong route handler", 500);
    }
}