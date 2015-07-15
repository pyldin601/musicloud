<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 27.06.15
 * Time: 14:40
 */

namespace app\core\http;


use app\abstractions\AbstractRepository;
use app\core\http;
use app\core\injector\Injectable;
use app\core\router;
use app\lang\functional\Sequence;
use app\lang\option\Collector;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;


class HttpParameter extends AbstractRepository implements SingletonInterface, Injectable {

    use Singleton;

    private $sources;

    public function __construct() {
        $this->sources = new Sequence();
        $this->sources->push(http\HttpGet::getInstance());
        $this->sources->push(http\HttpPut::getInstance());
        $this->sources->push(http\HttpPost::getInstance());
        $this->sources->push(router\RouteArgs::getInstance());
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        $isDefined = function (AbstractRepository $o) use (&$key) {
            return $o->isDefined($key);
        };
        return $this->sources->firstMatching($isDefined)->nonEmpty();
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getValue($key) {
        $unpack = function (AbstractRepository $support) use (&$key) {
            return $support->get($key);
        };
        return $this->sources->map($unpack)->reduce(Collector::optionCombine())->get();
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new http\NoArgumentException($key);
    }

}