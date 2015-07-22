<?php

namespace app\lang\option;



class Mapper {

    const NEW_INSTANCE_METHOD = "new";

    /**
     * @param $name
     * @param $args
     * @return callable
     */
    public static function method($name, ...$args) {
        return function ($obj) use (&$name, &$args) {
            return $obj->$name(...$args);
        };
    }

    /**
     * @param $name
     * @return \Closure
     */
    public static function field($name) {
        return function ($obj) use (&$name) {
            return $obj->$name;
        };
    }

    /**
     * @param $key
     * @return \Closure
     */
    public static function key($key) {
        return function ($arr) use (&$key) {
            return $arr[$key];
        };
    }

    /**
     * @return \Closure
     */
    public static function toBoolean() {
        return function ($value) {
            return boolval($value);
        };
    }

    /**
     * @return \Closure
     */
    public static function trim() {
        return function ($value) {
            return trim($value);
        };
    }

    /**
     * @return \Closure
     */
    public static function toNumber() {
        return function ($value) {
            return intval($value);
        };
    }

    /**
     * @param mixed $class Class name or object instance
     * @param string $method Method name to invoke
     * @return \Closure
     */
    public static function call($class, $method) {
        return function ($value) use (&$class, &$method) {
            return is_string($class)
                ? ($method === self::NEW_INSTANCE_METHOD
                    ? new $class($value)
                    : $class::$method($value))
                : $class->$method($value);
        };
    }

    /**
     * Returns NULL if $value is empty otherwise returns $value.
     * @return \Closure
     */
    public static function emptyToNull() {
        return function ($value) {
            return empty($value) ? null : $value;
        };
    }

    /**
     * @return callable
     */
    public static function fulltext() {
        return function ($text) {

            $query = "";
            $stop = "\\+\\-\\>\\<\\(\\)\\~\\*\\\"\\@";
            $words = preg_split("/(*UTF8)(?![\\p{L}|\\'|\\p{N}|\\#]+)|([$stop]+)/", $text);

            foreach ($words as $word) {
                if (strlen($word) > 0) {
                    $query .= "+{$word}";
                }
            }

            if (strlen($query))
                $query .= "*";

            return $query;

        };
    }

    /**
     * @param $that
     * @return callable
     */
    public static function min($that) {
        return function ($value) use ($that) {
            return ($that > $value) ? $value : $that;
        };
    }

    /**
     * @param $that
     * @return callable
     */
    public static function max($that) {
        return function ($value) use ($that) {
            return ($that < $value) ? $value : $that;
        };
    }

}


