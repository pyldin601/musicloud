<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.07.2015
 * Time: 14:28
 */

namespace app\core\http;


use app\core\exceptions\ControllerException;
use Exception;

class HttpException extends ControllerException {
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}