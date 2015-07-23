<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.07.15
 * Time: 20:01
 */

namespace app\core\cache;


use app\lang\option\Option;
use app\project\models\single\LoggedIn;

class RedisCache {

    const CACHE_NAME = "MEDIACLOUD";
    const CACHE_GLOBAL = self::CACHE_NAME . ":GLOBAL";

    const CACHE_TIMEOUT = 60;

    /** @var \Redis */
    private static $redis;

    /** @var LoggedIn */
    private static $me;

    public static function class_init() {
        self::$redis = RedisBackend::$redis;
        self::$me = resource(LoggedIn::class);
    }

    /**
     * @param $key
     * @return Option
     */
    public static function get($key) {

        $hash = self::hash($key);

        if (self::$redis->exists(self::CACHE_GLOBAL.":".$hash)) {

            return Option::Some(unserialize(self::$redis->get(self::CACHE_NAME.":".$hash)));

        } else {

            return Option::None();

        }

    }

    /**
     * @param $key
     * @param $value
     * @param null $timeout
     */
    public static function put($key, $value, $timeout = null) {
        $hash = self::hash($key);
        self::$redis->set(self::CACHE_GLOBAL.":".$hash,
            serialize($value), self::CACHE_TIMEOUT, $timeout ?: self::CACHE_TIMEOUT);
    }

    /**
     * @param $key
     */
    public static function erase($key) {
        $hash = self::hash($key);
        self::$redis->delete(self::CACHE_GLOBAL.":".$hash);
    }

    /**
     * @param $key
     * @return Option
     */
    public static function getMy($key) {

        $hash = self::hash($key);

        if (self::$redis->exists(self::myPrefix().":".$hash)) {

            return Option::Some(unserialize(self::$redis->get(self::myPrefix().":".$hash)));

        } else {

            return Option::None();

        }

    }

    /**
     * @param $key
     * @param $value
     * @param null $timeout
     */
    public static function putMy($key, $value, $timeout = null) {
        $hash = self::hash($key);
        self::$redis->set(self::myPrefix().":".$hash, serialize($value), $timeout ?: self::CACHE_TIMEOUT);
    }

    /**
     * @param $key
     */
    public static function eraseMy($key) {
        $hash = self::hash($key);
        self::$redis->delete(self::myPrefix().":".$hash);
    }

    /**
     * @return string
     */
    public static function myPrefix() {
        return self::CACHE_NAME . ":ID_" . self::$me->getId();
    }

    /**
     * @param $key
     * @return string
     */
    private static function hash($key) {
        return md5(serialize($key));
    }

}