<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 7/19/15
 * Time: 2:01 PM
 */

namespace app\core\helpers;


class UninstantiatedClass {
    private function __construct() {
        throw new \Exception(get_called_class() . " could not be instantiated");
    }
}