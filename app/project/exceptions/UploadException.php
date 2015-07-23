<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 23.07.2015
 * Time: 11:14
 */

namespace app\project\exceptions;


class UploadException extends BackendException {
    public function __construct($message = "", $http_response_code = 400) {
        parent::__construct($message, $http_response_code);
    }
}