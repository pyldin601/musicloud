<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.07.15
 * Time: 19:59
 */

namespace app\core\cache;


use app\core\etc\Settings;

class RedisBackend {

    /** @var \Redis */
    public static $redis;

    public static function class_init() {
        /** @var Settings $settings */
        $settings = resource(Settings::class);
        self::$redis = new \Redis();
        self::$redis->connect(
            $settings->get("redis", "hostname"),
            $settings->get("redis", "connect_port")
        );
    }

}