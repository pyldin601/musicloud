<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.07.15
 * Time: 19:59
 */

namespace app\core\cache;


class RedisBackend {
    public static $redis;
    public static function class_init() {
        self::$redis = new \Redis();
        self::$redis->connect("localhost", 6379);
    }
}