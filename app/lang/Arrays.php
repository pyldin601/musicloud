<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 15.07.15
 * Time: 23:36
 */

namespace app\lang;


class Arrays {
    public static function last(array $array) {
        return count($array) == 0 ? null : $array[count($array) - 1];
    }
    public static function first(array $array) {
        return count($array) == 0 ? null : $array[0];
    }
    public static function tail(array $array) {
        return array_slice($array, 1);
    }
    public static function init(array $array) {
        return array_slice($array, 0, count($array) - 1);
    }
    public static function drop($count, array $array) {
        return array_slice($array, $count);
    }
    public static function dropRight($count, array $array) {
        return array_slice($array, 0, count($array) - $count - 1);
    }
    public static function map($callback, array $array) {
        foreach ($array as $item) {
            yield $callback($item);
        }
    }
    public static function filter($callback, array $array) {
        foreach ($array as $item) {
            if ($callback($item))
                yield $item;
        }
    }
    public static function reduce($callback, array $array) {
        if (count($array) == 0) {
            throw new \Exception("Empty array could not be reduced!");
        } else if (count($array) == 1) {
            return $array[0];
        } else {
            $temp = $callback($array[0], $array[1]);
            foreach (self::drop(1, $array) as $item) {
                $temp = $callback($temp, $item);
            }
            return $temp;
        }
    }
    public static function allMatches($callback, array $array) {
        if (empty($array)) {
            return true;
        }
        foreach ($array as $item) {
            if (!$callback($item)) {
                return false;
            }
        }
        return true;
    }
    public static function any($callback, array $array) {
        if (empty($array)) {
            return true;
        }
        foreach ($array as $item) {
            if ($callback($item)) {
                return true;
            }
        }
        return false;
    }
    public static function contains($callback, array $array) {
        foreach ($array as $item) {
            if ($callback($item)) {
                return true;
            }
        }
        return false;
    }
}