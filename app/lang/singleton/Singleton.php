<?php

namespace app\lang\singleton;


use app\lang\option\Option;

trait Singleton {

    protected static $_instance = [];

    /**
     * @param $args
     * @return static
     */
    public static function getInstance(...$args) {
        $calledClass = get_called_class();
        $hash = $calledClass . " -> " . serialize($args);

        if (!isset(self::$_instance[$hash])) {
            self::$_instance[$hash] = new $calledClass(...$args);
        }
        return self::$_instance[$hash];
    }

    /**
     * @param $args
     * @return bool
     */
    public static function hasInstance(...$args) {
        $calledClass = get_called_class();
        $hash = $calledClass . " -> " . serialize($args);

        return isset(self::$_instance[$hash]) ? true : false;
    }

    /**
     * @param ...$args
     */
    public static function killInstance(...$args) {
        $calledClass = get_called_class();
        $hash = $calledClass . " -> " . serialize($args);

        unset(self::$_instance[$hash]);
    }

    /**
     * @param $args
     * @return Option
     */
    public static function ifInstance(...$args) {

        if (self::hasInstance(...$args)) {

            return Option::Some(self::getInstance(...$args));

        } else {

            return Option::None();

        }

    }

}
