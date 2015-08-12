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

function bool($value) {
    return $value ? "true" : "false";
}

/**
 * @param $url
 * @return string
 */
function escape_url($url) {
    $trimmed = str_replace("/", "%2F", trim($url));
    return $trimmed == "" ? "_" : urlencode($trimmed);
}