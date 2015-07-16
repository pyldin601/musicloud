<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 14:46
 */

namespace app\project\exceptions;


use app\core\exceptions\ControllerException;
use Exception;

class BackendException extends ControllerException {
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}