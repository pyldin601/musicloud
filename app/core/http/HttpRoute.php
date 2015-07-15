<?php

namespace app\core\http;


use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class HttpRoute implements SingletonInterface {

    use Singleton;

    const DEFAULT_ROUTE = "index";

    private $route, $raw;

    function __construct() {
        $http_get = new HttpGet();
        $this->raw = preg_replace('/(\.(html|php)$)|(\/$)/', '', $http_get->getOrElse("route", self::DEFAULT_ROUTE));
        $this->route = str_replace('/', '\\', FIXED_ROUTES_PATH . preg_replace_callback('/(?:.*\/)*(.+)$/', function ($match) {
                return "Do" . ucfirst($match[1]);
            }, $this->raw));
        $this->default_handler = Option::None();
    }

    /**
     * @return mixed
     */
    public function getRouteClass() {
        return $this->route;
    }

    /**
     * @return mixed
     */
    public function getRouteRaw() {
        return $this->raw;
    }

}