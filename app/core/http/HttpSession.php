<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:10
 */

namespace app\core\http;


use app\core\injector\Injectable;
use app\lang\Arrays;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class HttpSession implements SingletonInterface, Injectable {

    use Singleton;

    protected function __construct() {

        $lifetime = 2592000; // One month

        session_start();

        setcookie(session_name(), session_id(), time() + $lifetime, "/");

    }

    public function disableLifeTime() {

        setcookie(session_name(), session_id(), 0, "/");

    }

    /**
     * @param $keys
     * @return Option
     */
    public function get(...$keys) {
        $handle = &$_SESSION;
        foreach ($keys as $key) {
            if (isset($handle[$key])) {
                $handle = &$handle[$key];
            } else {
                return Option::None();
            }
        }
        return Option::Some($handle);
    }

    /**
     * @param $value
     * @param $keys
     */
    public function set($value, ...$keys) {
        $handle = &$_SESSION;
        foreach (Arrays::init($keys) as $key) {
            if (!isset($handle[$key])) {
                $handle[$key] = array();
            }
            $handle = &$handle[$key];
        }
        $handle[Arrays::last($keys)] = $value;
    }

    /**
     * @param $keys
     */
    public function erase(...$keys) {
        $handle = &$_SESSION;
        foreach (Arrays::init($keys) as $key) {
            if (isset($handle[$key])) {
                $handle = &$handle[$key];
            } else {
                return;
            }
        }
        unset($handle[Arrays::last($keys)]);
    }

} 