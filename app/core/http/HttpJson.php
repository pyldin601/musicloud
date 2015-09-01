<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 01.09.2015
 * Time: 15:44
 */

namespace app\core\http;


use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class HttpJson implements Injectable, SingletonInterface {
    use Singleton;
    /** @var array */
    public $data;
    public function __construct() {
        $this->data = json_decode(file_get_contents("php://input"));
    }
}