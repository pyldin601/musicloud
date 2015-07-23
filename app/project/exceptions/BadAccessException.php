<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:04
 */

namespace app\project\exceptions;


use app\core\http\HttpStatusCodes;

class BadAccessException extends BackendException {
    function __construct() {
        parent::__construct("You have no access to this resource", HttpStatusCodes::HTTP_FORBIDDEN);
    }
}