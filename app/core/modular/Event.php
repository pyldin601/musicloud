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
            self::getEventListeners($event, true)->add($callable);
        }
    }

    /**
     * @param $event
     * @param $callable
     */
    public static function addFilter($event, $callable) {
        if (is_callable($callable)) {
            self::getFilters($event, true)->add($callable);
        }
    }

    /**
     * @param $event
     * @param ...$arguments
     */
    public static function callEventListeners($event, ...$arguments) {
        if (($listeners = self::getEventListeners($event, false)) !== null) {
            foreach ($listeners as $listener) {
                $listener(...$arguments);
            }
        }
    }

    /**
     * @param $event
     * @param ...$arguments
     */
    public static function applyFilters($event, $argument) {
        $result = $argument;
        if (($filters = self::getFilters($event, false)) !== null) {
            foreach ($filters as $filter) {
                $result = $filter($result);
            }
        }
        return $result;
    }

    /**
     * @param $event
     * @return MLArray
     */
    private static function getEventListeners($event, $create) {

        if (isset(self::$events[$event])) {
            return self::$events[$event];
        } else if ($create === true) {
            self::$filters[$event] = new MLArray();
            return self::$filters[$event];
        } else {
            return null;
        }

    }

    /**
     * @param $event
     * @param $create
     * @return MLArray
     */
    private static function getFilters($event, $create) {

        if (isset(self::$filters[$event])) {
            return self::$filters[$event];
        } else if ($create === true) {
            self::$filters[$event] = new MLArray();
            return self::$filters[$event];
        } else {
            return null;
        }

    }
}