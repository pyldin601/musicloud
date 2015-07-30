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

    public static function join($src, $dst, $src_key, $dst_key) {
        $indices = [];
        $result = [];
        foreach ($dst as $item) {
            $indices[$item[$dst_key]] = $item;
        }
        foreach ($src as $item) if (isset($item[$src_key])) {
            $item[$src_key] = $indices[$item[$src_key]] ?: null;
            $result[] = $item;
        }
        return $result;
    }

    public static function generateRandomKey($length = 10) {

        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $key = "";
        while ($length --) {
            $key .= $charset[rand(0, strlen($charset) - 1)];
        }

        return $key;

    }

}