<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 29.06.15
 * Time: 15:46
 */

namespace app\core\http;


use app\abstractions\AbstractRepository;
use app\core\injector\Injectable;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class HttpFile extends AbstractRepository implements SingletonInterface, Injectable {

    use Singleton;

    private function __construct() {}

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        return array_key_exists($key, $_FILES);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getValue($key) {
        return $_FILES[$key];
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new NoArgumentException($key);
    }

    /**
     * @return Option
     */
    public function findAny() {
        if (count(array_keys($_FILES)) == 0) {
            return Option::None();
        } else {
            $key = array_keys($_FILES)[0];
            return Option::Some($_FILES[$key]);
        }
    }

    /**
     * @param $callable
     */
    public function each($callable) {
        foreach ($_FILES as $file) {
            $callable($file);
        }
    }

}