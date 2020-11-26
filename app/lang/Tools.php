<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:51
 */

namespace app\lang;


use app\lang\option\Mapper;

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
            if ($file === "." || $file === "..") {
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

    /**
     * @param $number
     * @param array $numbers
     * @return mixed
     */
    public static function closest($number, array $numbers) {
        $closest = null;
        foreach ($numbers as $item) {
            if ($closest === null || abs($number - $closest) > abs($item - $number)) {
                $closest = $item;
            }
        }
        return $closest;
    }

    public static function hashId($number) {
        $inv = [2, 3, 4, 7, 8, 9, 10, 13, 15, 17, 18, 19, 21, 23, 26, 28, 29, 31];
        $swp = [30, 21, 31, 21, 27, 31, 11, 17, 0, 25, 16, 24, 13, 12, 26, 15, 28, 30,
            18, 7, 19, 23, 4, 6, 3, 5, 9, 20, 29, 22, 1, 14];
        foreach ($inv as $n) {
            $number = $number ^ (1 << $n);
        }
        foreach ($swp as $from => $to) {
            $from_state = (($number >> $from) & 0x1);
            $to_state = (($number >> $to) & 0x1);
            if ($from_state ^ $to_state) {
                $number = $number ^ ((1 << $from) + (1 << $to));
            }
        }
        return $number;
    }

    public static function scan($path, $callable) {
        $content = scandir($path);
        print_r(array_filter("is_file", $content));

//        array_map(function ($f) use ($callable) { self::scan($f, $callable); },
//            array_filter("is_dir", $content));
    }

}
