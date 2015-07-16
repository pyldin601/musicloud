<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:01
 */

namespace app\project\exceptions;


class IncorrectPasswordException extends BackendException {
    public function __construct() {
        parent::__construct("Incorrect password", 403);
    }
}