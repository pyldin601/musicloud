<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 02.07.15
 * Time: 22:55
 */

namespace app\core\router\sources;


abstract class RouteSource {

    abstract function getCallable();

    abstract function getArgs();

}