<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 02.07.2015
 * Time: 13:53
 */

namespace app\core\router;


use app\core\exceptions\app\WrongRouteHandlerException;
use app\core\exceptions\ApplicationException;
use app\core\exceptions\ControllerException;
use app\core\exceptions\status\NotImplementedException;
use app\core\exceptions\status\PageNotFoundException;
use app\core\http\HttpRoute;
use app\core\injector\Injector;
use app\core\router\sources\RouteSource;
use app\core\view\JsonResponse;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class Router implements SingletonInterface {

    use Singleton;

    private $routes = array();
    private $default_handler;

    /** @var HttpRoute */
    private $route;

    public function __construct() {

        $this->default_handler = Option::None();

        $this->route = HttpRoute::getInstance();

    }

    /**
     * @param $pattern
     * @param $callable
     */
    public function when($pattern, $callable) {

        list($regexp, $keys) = $this->parametrize($pattern);

        $this->routes[$regexp] = array(
            "keys" => $keys,
            "action" => $callable
        );

    }

    /**
     * @param $regexp
     * @param $callable
     */
    public function whenRegExp($regexp, $callable) {

        $this->routes[$regexp] = array(
            "keys" => [],
            "action" => $callable
        );

    }

    /**
     * @param $callable
     */
    public function otherwise($callable) {

        $this->default_handler = Option::Some(array(
            "action" => $callable,
            "args" => array()
        ));

    }

    /**
     * @param String $route
     * @return array
     */
    private function parametrize($route) {

        $quoted = preg_replace_callback("~(?!:([a-z\\_]+))|(?!&([a-z\\_]+))~", function ($match) {
            return preg_quote($match[0]);
        }, $route);

        $keys = array();

        $quote_params =

            preg_replace_callback("~&([a-z\\_]+)~", function ($match) use (&$keys) {
                $keys[] = $match[1];
                return "(?:(\\d+))";
            },

                preg_replace_callback("~:([a-z\\_]+)~", function ($match) use (&$keys) {
                    $keys[] = $match[1];
                    return "(?:([^\\/]*))";
                }, $quoted));

        return array(sprintf("~^%s$~", $quote_params), $keys);

    }

    /**
     * @return Option
     */
    public function findDynamicRoute() {

        $raw = $this->route->getRouteRaw();

        foreach ($this->routes as $regexp => $data) {

            if (preg_match($regexp, $raw, $matches) || preg_match($regexp, $raw . "/", $matches)) {

                array_shift($matches);

                $args = array_combine($data["keys"], $matches);

                return Option::Some(array(
                    "action" => $data["action"],
                    "args" => $args
                ));

            }

        }

        return Option::None();

    }

    public function findHardRoute() {

        if (class_exists($this->route->getRouteClass())) {
            return Option::Some(array(
                "action" => $this->route->getRouteClass(),
                "args" => array()
            ));
        }

        return Option::None();

    }

    /**
     * @return Option
     */
    public function findDefaultRoute() {

        return $this->default_handler;

    }

    /**
     * @return Option
     */
    public function find() {

        return $this->findHardRoute()
            ->orElse($this->findDynamicRoute())
            ->orElse($this->findDefaultRoute());

    }


    public function run() {

        $handler = $this->find()->getOrThrow(PageNotFoundException::class);

        RouteArgs::getInstance()->setMapData($handler["args"]);

        if (is_string($handler["action"])) {

            if (!class_exists($handler["action"])) {
                throw new PageNotFoundException;
            }

            $instance = new $handler["action"];

            if (!$instance instanceof RouteHandler) {
                throw new WrongRouteHandlerException;
            }

            $method = "do" . ucfirst(strtolower($_SERVER["REQUEST_METHOD"]));

            if (!method_exists($instance, $method)) {
                throw new NotImplementedException;
            }

            Injector::run(array($instance, $method));

        } else if (is_callable($handler["action"])) {

            Injector::run($handler["action"]);

        } else {

            throw new ApplicationException("Invalid action handler!");

        }

    }

}