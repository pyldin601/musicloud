<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 27.06.15
 * Time: 14:35
 */

namespace app\core\router;


use app\abstractions\AbstractRepository;
use app\core\http\NoArgumentException;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class RouteArgs extends AbstractRepository implements SingletonInterface {

    use Singleton;

    private $map = [];

    /**
     * @param string $key
     * @return bool
     */
    public function isDefined($key) {
        return array_key_exists($key, $this->map);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getValue($key) {
        return $this->map[$key];
    }

    /**
     * @param $key
     * @return \Exception
     */
    protected function getException($key) {
        return new NoArgumentException($key);
    }

    /**
     * @param array $data
     */
    public function setMapData(array $data) {
        $this->map = $data;
    }

}