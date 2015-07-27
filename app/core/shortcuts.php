<?php

use app\core\router\Router;

/**
 * Adds new dynamic route to router pool
 * @param $pattern
 * @param $callable
 */
function when($pattern, $callable) {
    Router::getInstance()->when($pattern, $callable);
}

/**
 * @param $regexp
 * @param $callable
 */
function whenRegExp($regexp, $callable) {
    Router::getInstance()->whenRegExp($regexp, $callable);
}

/**
 * Adds default route to router pool
 * @param $callable
 */
function otherwise($callable) {
    Router::getInstance()->otherwise($callable);
}