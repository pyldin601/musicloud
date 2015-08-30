<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 30.08.15
 * Time: 17:42
 */

namespace app\core\modular;


use app\lang\MLArray;

class Event {

    /**
     * @var array
     */
    private static $events = [];

    /**
     * @var array
     */
    private static $filters = [];

    /**
     * @param $event
     * @param $callable
     */
    public static function addEventListener($event, $callable) {
        if (is_callable($callable)) {
            self::getEventListeners($event)->add($callable);
        }
    }

    /**
     * @param $event
     * @param $callable
     */
    public static function addFilter($event, $callable) {
        if (is_callable($callable)) {
            self::getFilters($event)->add($callable);
        }
    }

    /**
     * @param $event
     * @param ...$arguments
     */
    public static function callEventListeners($event, ...$arguments) {
        foreach (self::getEventListeners($event) as $listener) {
            $listener(...$arguments);
        }
    }

    /**
     * @param $event
     * @param ...$arguments
     */
    public static function applyFilters($event, $argument) {
        $result = $argument;
        foreach (self::getFilters($event) as $filter) {
            $result = $filter($result);
        }
        return $result;
    }

    /**
     * @param $event
     * @return MLArray
     */
    private static function &getEventListeners($event) {

        if (!isset(self::$events[$event])) {
            self::$events[$event] = new MLArray();
        }

        return self::$events[$event];

    }

    /**
     * @param $event
     * @return MLArray
     */
    private static function &getFilters($event) {
        if (!isset(self::$filters[$event])) {
            self::$filters[$event] = new MLArray();
        }
        return self::$filters[$event];
    }
}