<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 17.08.2015
 * Time: 15:59
 */

namespace app\core\exceptions;


use app\core\http\HttpStatusCodes;

class ValidatorException extends ControllerException {
    public function __construct($message = "", $http_response_code = HttpStatusCodes::HTTP_BAD_REQUEST) {
        parent::__construct($message, $http_response_code);
    }
}