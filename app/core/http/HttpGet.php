<?php

namespace app\core\http;


use app\abstractions\AbstractRepository;
use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class HttpGet extends AbstractRepository implements SingletonInterface, Injectable {

    use Singleton;

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        return isset($_GET[$key]);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getValue($key) {
        return $_GET[$key];
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new NoArgumentException($key);
    }

}