<?php

namespace app\core\http;


class NoArgumentException extends HttpException {
    public function __construct($key) {
        parent::__construct($key . " - argument not set", 400);
    }
}