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

/**
 * @param $fh
 * @param int $length
 * @param int $buffer_length
 * @return string
 */
function read($fh, $length, $buffer_length = 2048) {
    $blocks = intval($length / $buffer_length);
    $rest = $length % $buffer_length;
    $acc = "";
    for ($i = 0; $i < $blocks && !feof($fh); $i ++) {
        $acc .= fread($fh, $buffer_length);
    }
    if (!feof($fh)) {
        $acc .= fread($fh, $rest);
    }
    return $acc;
}

/**
 * @param array $array
 * @return string
 */
function array_to_string(array $array) {
    $all = [];
    foreach ($array as $key => $val) {
        $all[] = $key . "=" . $val;
    }
    return "(" . implode(", ", $all) . ")";
}

/**
 * @param $delimiter
 * @param $string
 * @return array
 * @throws Exception
 */
function ml_explode($delimiter, $string) {
    if (!is_string($delimiter) || $delimiter === "") {
        throw new Exception("Delimited must be non-empty string");
    }
    if ($count = substr_count($string, $delimiter) === 0) {
        return array($string);
    } else {
        return explode($delimiter, $string, $count + 1);
    }
}
