<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 27.06.15
 * Time: 14:29
 */

namespace app\core\http;


use app\abstractions\AbstractRepository;
use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;


class HttpPost extends AbstractRepository implements SingletonInterface, Injectable {

    use Singleton;

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        return array_key_exists($key, $_POST);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getValue($key) {
        return $_POST[$key];
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new NoArgumentException($key);
    }


}