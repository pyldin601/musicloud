<?php

namespace app\lang\option;


final class None extends Option {

    private static $_instance = null;

    private function __construct() {
    }

    public static function instance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function isEmpty() {
        return true;
    }

    public function get() {
        throw new OptionException("No such element");
    }

    public function __toString() {
        return "None";
    }

    public function getIterator() {
        return new \EmptyIterator();
    }

    public function nonEmpty() {
        return false;
    }

    public function getOrElse($other) {
        return $other;
    }

    public function orFalse() {
        return false;
    }

    public function orZero() {
        return 0;
    }

    public function orNull() {
        return null;
    }

    public function orEmpty() {
        return "";
    }

    public function orCall($callable) {
        return $callable();
    }

    public function orElse(Option $alternative) {
        return $alternative;
    }

    public function getOrThrow($exception, ...$args) {

        if (is_string($exception)) {

            $reflection = new \ReflectionClass($exception);
            $obj = $reflection->newInstanceArgs($args);
            if ($obj instanceof \Exception) {
                throw $obj;
            } else {
                throw new OptionException("Invalid exception passed");
            }

        } else if ($exception instanceof \ReflectionMethod && $exception->isStatic()) {
            throw $exception->invokeArgs(null, $args);
        } else if ($exception instanceof \Exception) {
            throw $exception;
        }

    }


    /**
     * @param $callable
     * @param ...$args
     * @return $this
     */
    public function orThrow($callable, ...$args) {
        $this->getOrThrow($callable, ...$args);
        return $this;
    }


    public function map($callable, ...$args) {
        return $this;
    }

    public function flatMap($callable) {
        return $this;
    }

    public function filter($predicate) {
        return $this;
    }

    public function reject($predicate) {
        return $this;
    }

    public function toInt() {
        return $this;
    }

    public function then($callable, $otherwise = null) {
        if (is_callable($otherwise)) {
            return $otherwise();
        }
        return $this;
    }

    /**
     * @param \Closure $producer
     * @return Option
     */
    public function otherwise(\Closure $producer) {
        return Option::Some($producer());
    }


    public function select($value) {
        return $this;
    }

    public function selectInstance($object) {
        return $this;
    }


}

