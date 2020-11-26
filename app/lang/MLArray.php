<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.08.2015
 * Time: 9:01
 */

namespace app\lang;


use app\lang\option\Option;

class MLArray implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable {

    /** @var array */
    private $contents = array();

    public function __construct(array $initial = null) {
        if ($initial !== null) {
            $this->contents = $initial;
        }
    }

    public function offsetExists($offset) {
        return isset($this->contents[$offset]);
    }

    public function offsetGet($offset) {
        return $this->contents[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->contents[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset ($this->contents[$offset]);
    }

    /**
     * @param $callback
     * @return MLArray
     */
    public function map($callback) {
        return new self(array_map($callback, $this->contents));
    }

    /**
     * @param $callback
     * @return MLArray
     */
    public function filter($callback) {
        return new self(array_filter($this->contents, $callback));
    }

    /**
     * @param $callback
     * @param null $initial
     * @return mixed
     */
    public function reduce($callback, $initial = null) {
        return array_reduce($this->contents, $callback, $initial);
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->contents);
    }

    /**
     * @return \Iterator
     */
    public function getIterator() {
        foreach ($this->contents as $value) {
            yield $value;
        }
    }

    public function all($callable) {
        foreach ($this->contents as $value) {
            if ($callable($value) === false) {
                return false;
            }
        }
        return true;
    }

    public function any($callable) {
        foreach ($this->contents as $value) {
            if ($callable($value) === true) {
                return true;
            }
        }
        return false;
    }

    public function contains($value) {
        return in_array($value, $this->contents);
    }

    public function indexOf($value) {
        return array_search($value, $this->contents);
    }

    public function add($value) {
        $this->contents[] = $value;
    }

    public function removeByIndex($index) {
        unset($this->contents[$index]);
        $this->contents = array_values($this->contents);
    }

    public function removeByValue($value) {
        $index = array_search($value, $this->contents);
        if ($index !== false) {
            $this->removeByIndex($index);
        }
    }

    /**
     * @param $delimiter
     * @param $text
     * @return MLArray
     */
    public static function split($delimiter, $text) {
        if ($delimiter === "") {
            return new self(str_split($text));
        } else {
            $count = substr_count($text, $delimiter);
            if ($count === 0 && $text === "") {
                return new self();
            } elseif ($count === 0) {
                return new self([$text]);
            } else {
                return new self(explode($delimiter, $text, $count + 1));
            }
        }
    }

    /**
     * @return array
     */
    public function mkArray() {
        return $this->contents;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->contents;
    }

    /**
     * @return array
     */
    public function keys() {
        return array_keys($this->contents);
    }

    /**
     * @return array
     */
    public function values() {
        return array_values($this->contents);
    }

}
