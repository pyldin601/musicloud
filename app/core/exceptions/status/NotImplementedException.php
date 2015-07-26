<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 9:50
 */

namespace app\core\exceptions\status;


use app\core\exceptions\StatusException;
use app\core\http\HttpStatusCodes;

class NotImplementedException extends StatusException {
    public function __construct() {
        parent::__construct("Requested Method Is Not Implemented", HttpStatusCodes::HTTP_NOT_IMPLEMENTED);
    }
}