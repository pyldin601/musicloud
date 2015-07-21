<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:47
 */

namespace app\core\view;


use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class JsonResponse implements SingletonInterface, Injectable {

    // todo: JSON if content-type specified

    use Singleton;

    private $data;

    protected function __construct() { }

    public function write($object) {

        $this->data = &$object;

    }

    public function __destruct() {

        header("Content-Type: application/json");

        echo json_encode($this->data ?: "OK");

    }

} 