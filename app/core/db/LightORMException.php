<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 25.08.15
 * Time: 23:14
 */

namespace app\core\db;


use Exception;

class LightORMException extends \Exception {
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}