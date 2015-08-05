<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 05.08.2015
 * Time: 10:28
 */

namespace app\core\logging;


use app\lang\Tools;

class Logger {
    private static $session_id;
    public static function class_init() {
        self::$session_id = Tools::generateRandomKey(5);
    }
    public static function printf($message, ...$args) {
        error_log(sprintf(self::$session_id . " :: " . $message, ...$args));
    }
    public static function exception(\Exception $exception) {
        error_log(self::$session_id . " :: Exception: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
    }
} 