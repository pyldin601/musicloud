<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 9:45
 */

namespace app\core\exceptions;


use app\core\http\HttpStatusCodes;
use Exception;

class StatusException extends ApplicationException {
    public function __construct($message = "", $http_response_code = HttpStatusCodes::HTTP_BAD_REQUEST) {
        parent::__construct($message, $http_response_code);
    }
}