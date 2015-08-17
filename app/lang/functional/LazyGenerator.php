<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 17.08.2015
 * Time: 11:36
 */

namespace app\lang\functional;


class LazyGenerator implements \IteratorAggregate {
    /** @var \Generator */
    private $generator;

    public function __construct(\Generator $generator) {
        $this->generator = $generator;
    }

    public function map($callback) {
        $gen = function () use ($callback) {
            foreach ($this->generator as $item) {
                yield $callback($item);
            }
        };
        return new self($gen());
    }

    public function filter($predicate) {
        $gen = function () use ($predicate) {
            foreach ($this->generator as $item) {
                if ($predicate($item)) {
                    yield $item;
                }
            }
        };
        return new self($gen());
    }

    public function reduce($operation, $initial = null) {
        $temp = $initial;
        foreach ($this->generator as $item) {
            if (is_null($temp)) {
                $temp = $item;
            } else {
                $temp = $operation($temp, $item);
            }
        }
        return $temp;
    }

    public function getIterator() {
        return $this->generator;
    }

} 