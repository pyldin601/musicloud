<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.07.2015
 * Time: 14:34
 */

namespace app\core\exceptions;


use app\core\http\HttpStatusCodes;

class ControllerException extends ApplicationException {
    public function __construct($message = "", $http_response_code = HttpStatusCodes::HTTP_BAD_REQUEST) {
        parent::__construct($message, $http_response_code);
    }
}