<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 14:47
 */

namespace app\project\exceptions;


use Exception;

class UserNotFoundException extends BackendException {
    public function __construct() {
        parent::__construct("User not found", 404);
    }
}