<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.07.2015
 * Time: 14:34
 */

namespace app\core\exceptions;


use Exception;

class ControllerException extends ApplicationException {
    public function __construct($message = "", $http_response_code = 400) {
        parent::__construct($message, $http_response_code);
    }
}