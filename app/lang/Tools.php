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

    /**
     * @param string $path
     * @param callable $walker
     * @return boolean
     */
    public static function fsWalker($path, $walker) {
        $fh = opendir($path);
        while ($file = readdir($fh)) {
            if ($file == "." || $file == "..") {
                continue;
            }
            $full_name = $path . "/" . $file;
            if (is_dir($path . "/" . $file)) {
                if (self::fsWalker($full_name, $walker) === false)
                    return false;
            } else {
                if ($walker($full_name) === false)
                    return false;
            }
        }
        return true;
    }
} 