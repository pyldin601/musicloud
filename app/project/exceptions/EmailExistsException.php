<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 7/19/15
 * Time: 1:25 PM
 */

namespace app\project\exceptions;


class EmailExistsException extends BackendException {
    public function __construct() {
        parent::__construct("User with this email already registered", 400);
    }
}