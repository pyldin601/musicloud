<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 14:46
 */

namespace app\project\exceptions;


use app\core\exceptions\ControllerException;
use app\core\http\HttpStatusCodes;
use Exception;

class BackendException extends ControllerException {
    public function __construct($message = "", $http_response_code = HttpStatusCodes::HTTP_BAD_REQUEST) {
        parent::__construct($message, $http_response_code);
    }
}