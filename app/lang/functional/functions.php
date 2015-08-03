<?php

/**
 * Returns true if all elements in given array are
 * equivalent to true.
 *
 * @param array $array
 * @return bool
 */
function all(array &$array) {
    foreach ($array as &$item) {
        if (! $item) {
            return false;
        }
    }
    return true;
}

/**
 * Returns true if at least one element in array are
 * equivalent to true.
 *
 * @param array $array
 * @return bool
 */
function any(array $array) {
    foreach ($array as $item) if ($item) return true;
    return false;
}

/**
 * Returns a list of arrays, where the i-th array contains
 * the i-th element from each of the argument sequences
 * or iterables. The returned list is truncated in length
 * to the length of the shortest argument sequence.
 *
 * @param $arrays
 * @return array
 */
function zip(...$arrays) {
    return array_map(null, ...$arrays);
}

/**
 * Doing $callable with opened file $filename with mode $mode and
 * automatically closes file at finish.
 *
 * @param string $filename
 * @param string $mode
 * @param Callable $callable
 */
function withOpenedFile($filename, $mode, $callable) {
    try {
        $fh = fopen($filename, $mode);
        $callable($fh);
    } finally {
        fclose($fh);
    }
}
