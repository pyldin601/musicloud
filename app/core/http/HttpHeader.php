<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 29.06.15
 * Time: 19:06
 */

namespace Framework\Services\Http;


use app\abstractions\AbstractRepository;
use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use http\Env;

class HttpHeader extends AbstractRepository implements SingletonInterface, Injectable {

    use Singleton;

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        return Env::getRequestHeader($key) !== null;
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getValue($key) {
        return Env::getRequestHeader($key);
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new \Exception("Hello");
    }

}