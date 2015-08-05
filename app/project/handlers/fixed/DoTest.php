<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 01.08.15
 * Time: 00:14
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;

class DoTest implements RouteHandler {
    public function doGet() {
        header("Content-Type: text/plain");
        $i = 10;
        while ($i -- > 0) {
            echo "Hello, World!" . PHP_EOL;
            flush();
            sleep(1);
        }
        echo "Done";
    }
}

