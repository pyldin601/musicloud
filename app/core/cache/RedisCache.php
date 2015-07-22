<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.07.15
 * Time: 20:01
 */

namespace app\core\cache;


use app\lang\option\Option;

class RedisCache {

    const CACHE_NAME = "HomeMusic";

    /** @var \Redis */
    private static $redis;

    public static function class_init() {
        self::$redis = RedisBackend::$redis;
    }

    /**
     * @param $key
     * @return Option
     */
    public static function get($key) {
        $hash = self::hash($key);
        if (self::$redis->hExists(self::CACHE_NAME, $hash)) {
            return Option::Some(unserialize(self::$redis->hGet(self::CACHE_NAME, $hash)));
        } else {
            return Option::None();
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public static function put($key, $value) {
        $hash = self::hash($key);
        self::$redis->hSet(self::CACHE_NAME, $hash, serialize($value));
    }

    /**
     * @param $key
     * @return string
     */
    private static function hash($key) {
        return md5(serialize($key));
    }

}