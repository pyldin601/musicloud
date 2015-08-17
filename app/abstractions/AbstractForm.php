<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:55
 */

namespace app\abstractions;


use app\core\http\HttpPost;
use app\core\injector\Injectable;

abstract class AbstractForm implements Injectable {

    protected function __construct() {

        $post = HttpPost::getInstance();

        /** @var \ReflectionProperty $property */
        foreach ((new \ReflectionClass($this))->getProperties() as $property) {
            if ($property->isStatic() || substr($property->getName(), 0, 1) == "_")
                continue;

            $property->setAccessible(true);
            $property->setValue($this, $post->get($property->getName()));
        }

        $this->validate();

    }

    public function __toString() {

        $str = array();

        /** @var \ReflectionProperty $property */
        foreach ((new \ReflectionClass($this))->getProperties() as $property) {
            if ($property->isStatic() || substr($property->getName(), 0, 1) == "_")
                continue;

            $property->setAccessible(true);

            $str[] = $property->getName() . "=" . $property->getValue($this);

        }

        return "Form(" . implode(", ", $str) .  ")";

    }

    protected abstract function validate();

} 