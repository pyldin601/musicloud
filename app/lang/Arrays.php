<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 15.07.15
 * Time: 23:36
 */

namespace app\lang;


class Arrays {
    public static function last(array $array) {
        return count($array) == 0 ? null : $array[count($array) - 1];
    }
}