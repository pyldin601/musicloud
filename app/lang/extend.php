<?php

/**
 * Returns TRUE if substring $needle found in $haystack
 * otherwise returns FALSE.
 *
 * @param $needle
 * @param $haystack
 * @return bool
 */
function in_string($needle, $haystack) {
    return strpos($haystack, $needle, 0) !== false;
}

/**
 * Returns TRUE whereas $string is empty or FALSE if not.
 *
 * @param $string
 * @return bool
 */
function is_empty($string) {
    return empty($string);
}