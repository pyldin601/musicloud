<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:23
 */

namespace app\project\exceptions;


class AssertException extends BackendException {
    public function __construct($message) {
        parent::__construct($message, 400);
    }
} 