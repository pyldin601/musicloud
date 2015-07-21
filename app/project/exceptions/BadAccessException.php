<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:04
 */

namespace app\project\exceptions;


class BadAccessException extends BackendException {
    function __construct() {
        parent::__construct("You have no access to modify this resource", 403);
    }
}