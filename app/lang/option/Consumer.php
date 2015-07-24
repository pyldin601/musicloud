<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 24.07.2015
 * Time: 16:00
 */

namespace app\lang\option;


class Consumer {

    /**
     * @param $function
     * @param $args
     * @return callable
     */
    public static function call($function, ...$args) {
        return function () use ($function, $args) {
            call_user_func_array($function, $args);
        };
    }
}