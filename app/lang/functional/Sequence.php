<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.06.2015
 * Time: 14:04
 */

namespace app\lang\functional;


use app\lang\option\Option;

class Sequence implements \Countable, \IteratorAggregate, \JsonSerializable {

    private $array = [];

    /**
     * @param array|null $array
     */
    public function __construct(array $array = null) {
        if (is_array($array)) {
            $this->array = $array;
        }
    }

    /**
     * @return mixed
     */
    public function head() {
        return $this->array[0];
    }

    /**
     * @return Sequence
     */
    public function tail() {
        return new self(array_slice($this->array, 1));
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->array);
    }

    /**
     * @param $callback
     * @return Sequence
     */
    public function map($callback) {
        return new self(array_map($callback, $this->array));
    }

    /**
     * @param $predicate
     * @param int $flag
     * @return Sequence
     * @internal param int $flag
     */
    public function filter($predicate, $flag = 0) {
        return new self(array_values(array_filter($this->array, $predicate, $flag)));
    }

    /**
     * @param $callback
     * @return Sequence
     */
    public function sort($callback) {
        $items = $this->array;
        usort($items, $callback);
        return new self($items);
    }

    /**
     * @param $callback
     * @param null $initial
     * @return mixed
     */
    public function reduce($callback, $initial = null) {
        if (count($this->array) == 0) {
            return null;
        }
        if ($initial == null) {
            $head = $this->array[0];
            $rest = array_slice($this->array, 1);
            return array_reduce($rest, $callback, $head);
        }
        return array_reduce($this->array, $callback, $initial);
    }

    /**
     * @param $callback
     * @return Option
     */
    public function firstMatching($callback) {
        foreach ($this->array as $item) {
            if ($callback($item)) {
                return Option::Some($item);
            }
        }
        return Option::None();
    }

    /**
     * @return Sequence
     */
    public function odd() {
        return $this->filter(function ($value, $index) { return $index % 2 == 1; },
            ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @return Sequence
     */
    public function even() {
        return $this->filter(function ($value, $index) { return $index % 2 == 0; },
            ARRAY_FILTER_USE_BOTH);
    }


    /**
     * @return mixed
     */
    public function sum() {
        return $this->reduce(function ($a, $b) { return $a + $b; }, 0);
    }

    /**
     * @return mixed
     */
    public function max() {
        return max($this->array);
    }

    /**
     * @return mixed
     */
    public function min() {
        return min($this->array);
    }

    /**
     * @param $glue
     * @return string
     */
    public function concat($glue) {
        return implode($glue, $this->array);
    }

    /**
     * @param $value
     */
    public function push($value) {
        array_push($this->array, $value);
    }

    /**
     * @return mixed
     */
    public function shift() {
        return array_shift($this->array);
    }

    /**
     * @param $value
     */
    public function unshift($value) {
        array_unshift($this->array, $value);
    }

    /**
     * @return mixed
     */
    public function pop() {
        return array_pop($this->array);
    }

    /**
     * @return \Iterator
     */
    public function getIterator() {
        foreach ($this->array as $value) {
            yield $value;
        }
    }

    /**
     * @return array
     */
    public function asArray() {
        return $this->array;
    }

    public function clear() {
        $this->array = [];
    }

    /**
     * @return array
     */
    function jsonSerialize() {
        return $this->array;
    }

    /**
     * @return Sequence
     */
    public function reverse() {
        return new self(array_reverse($this->array, false));
    }

    /**
     * @param $size
     * @param $value
     * @return Sequence
     */
    public function pad($size, $value) {
        return new self(array_pad($this->array, $size, $value));
    }

    /**
     * @param $callback
     * @param ...$args
     */
    public function each($callback, ...$args) {
        foreach ($this->array as $value) {
            $callback($value, ...$args);
        }
    }

    /**
     * @param $delimiter
     * @param $str
     * @param null $limit
     * @return Sequence
     */
    public static function split($delimiter, $str, $limit = null) {
        return new self(
            is_null($limit)
                ? explode($delimiter, $str)
                : explode($delimiter, $str, $limit)
        );
    }

    /**
     * @param $className
     * @return Sequence
     */
    public function wrap($className) {
        return $this->map(function ($value) use (&$className) {
            return new $className($value);
        });
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return count($this->array) == 0;
    }

    /**
     * @param $throwable
     * @throws \Exception
     */
    public function throwIfEmpty($throwable) {
        if ($this->isEmpty()) {
            if ($throwable instanceof \Exception) {
                throw $throwable;
            } else {
                throw new $throwable();
            }
        }
    }

}