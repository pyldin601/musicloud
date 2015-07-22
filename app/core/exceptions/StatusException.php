<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 9:45
 */

namespace app\core\exceptions;


use Exception;

class StatusException extends ApplicationException {
    public function __construct($message = "", $http_response_code = 400) {
        parent::__construct($message, $http_response_code);
    }
}