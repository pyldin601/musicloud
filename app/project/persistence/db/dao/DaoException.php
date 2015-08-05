<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 05.08.2015
 * Time: 12:36
 */

namespace app\project\persistence\db\dao;


use app\core\exceptions\ApplicationException;
use app\core\http\HttpStatusCodes;

class DaoException extends ApplicationException {
    public function __construct($message = "", $http_response_code = HttpStatusCodes::HTTP_BAD_REQUEST) {
        parent::__construct($message, $http_response_code);
    }
}