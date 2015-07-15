<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 25.02.15
 * Time: 11:07
 */

namespace app\core\injector;


use app\core\exceptions\ControllerException;
use Exception;

class InjectorException extends ControllerException {
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}