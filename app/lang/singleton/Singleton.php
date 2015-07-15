<?php

namespace app\lang\singleton;


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
            $reflector = new \ReflectionClass($calledClass);
            self::$_instance[$hash] = $reflector->newInstanceArgs($args);
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
}
