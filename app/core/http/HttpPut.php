<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 27.06.15
 * Time: 14:32
 */

namespace app\core\http;


use app\abstractions\AbstractRepository;
use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;


class HttpPut extends AbstractRepository implements SingletonInterface, Injectable {

    use Singleton;

    private $_PUT;

    public function __construct() {
        parse_str(file_get_contents("php://input"), $this->_PUT);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        return array_key_exists($key, $this->_PUT);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getValue($key) {
        return $this->_PUT[$key];
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new NoArgumentException($key);
    }

}