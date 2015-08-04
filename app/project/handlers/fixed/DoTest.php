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
    public function doGet(HttpGet $get) {
        $value = $get->get("id")
            ->orThrow(ApplicationException::class, "ID not specified!")
            ->filter("is_number")
            ->orThrow(ApplicationException::class, "ID must me a number!")
            ->reject(0)
            ->getOrThrow(ApplicationException::class, "ID could not be zero!");

        echo "Your ID is " . $value;
    }
}

