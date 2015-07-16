<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:51
 */

namespace app\lang;


class Tools {
    /**
     * @param $args
     * @return bool
     */
    public static function isNull(...$args) {
        foreach ($args as $arg) {
            if ($arg === null) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $source
     * @return mixed
     */
    public static function turnSlashes($source) {
        return str_replace("/", "\\", $source);
    }
} 