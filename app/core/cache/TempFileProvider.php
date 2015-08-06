<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 13:14
 */

namespace app\core\cache;


use app\core\logging\Logger;

class TempFileProvider {

    public static $temp_path;

    private static $temp_files = [];

    public static function class_init() {
        register_shutdown_function(function () {
            foreach (self::$temp_files as $temp_file) {
                if (file_exists($temp_file)) {
                    unlink($temp_file);
                }
            }
        });
    }

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
            $temp_file = self::$temp_path . "/{$prefix}_" . md5(rand(0, 1000000000)) . $suffix;
        } while (file_exists($temp_file));
        Logger::printf("Generating temp file %s", $temp_file);
        self::$temp_files[] = $temp_file;
        return $temp_file;
    }

    /**
     * @param $file_name
     */
    public static function delete($file_name) {
        if (in_array($file_name, self::$temp_files)) {
            unlink($file_name);
        }
    }


} 