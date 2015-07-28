<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 13:14
 */

namespace app\core\cache;


class TempFileProvider {

    public static $temp_path;

    /**
     * @param $temp_path
     */
    public static function setTempPath($temp_path) {
        self::$temp_path = $temp_path;
    }

    /**
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public static function generate($prefix = "file", $suffix = "") {
        do {
            $file = self::$temp_path . "/{$prefix}_" . md5(rand(0, 1000000000)) . $suffix;
        } while (file_exists($file));
        delete_on_shutdown($file);
        return $file;
    }

} 