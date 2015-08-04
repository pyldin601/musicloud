<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 01.08.15
 * Time: 00:14
 */

namespace app\project\handlers\fixed;


use app\core\exceptions\ApplicationException;
use app\core\http\HttpGet;
use app\core\router\RouteHandler;

class DoTest implements RouteHandler {
    public function doGet(HttpGet $httpGet) {
        $value = $httpGet->get("id")
            ->orThrow(ApplicationException::class, "ID not specified!")
            ->filter("is_numeric")
            ->orThrow(ApplicationException::class, "ID must be a number!")
            ->map("intval")
            ->reject(0)
            ->getOrThrow(ApplicationException::class, "ID could not be zero!");

        echo "Your ID is " . $value;
    }
}

