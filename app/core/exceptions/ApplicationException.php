<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 9:44
 */

namespace app\core\exceptions;


use app\core\http\HttpStatusCodes;
use Exception;

class ApplicationException extends \Exception {

    private $http_code;

    public function __construct($message = "", $http_response_code = HttpStatusCodes::HTTP_BAD_REQUEST) {
        parent::__construct($message, 0);
        $this->http_code = $http_response_code;
    }

    public function getHttpCode() {
        return $this->http_code;
    }

}