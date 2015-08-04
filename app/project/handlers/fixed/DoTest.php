<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 01.08.15
 * Time: 00:14
 */

namespace app\project\handlers\fixed;


use app\core\router\RouteHandler;
use app\lang\Tools;

class DoTest implements RouteHandler {
    public function doGet() {
        header("Content-Type: text/plain");
        Tools::scan("/tmp", function ($file) {
            echo $file . PHP_EOL;
        });
    }
}

function shiftBits($number) {
    $size = 0x20;
    $number = $number ^ 0xFF71A723;
    for ($i = 0; $i < $size; $i++) {
        $to = ($i * 3) % $size;
        if ((($number >> $i) & 0x01) ^ (($number >> $to) & 0x01)) {
            $number = $number ^ ((1 << $i) + (1 << $to));
        }
    }
    return $number;
}

function alpha($number) {
    $number = abs($number);
    $chars = "n92V1Dodry7FZzzY8lJ0svakSO6PpIHtBRcTgW45LUfNA-wqiuXMbCejGhKQmE3";
    $result = "";
    do {
        $result .= $chars[$number & 0x3F];
        $number >>= 6;
    } while ($number > 0);
    return $result;
}
