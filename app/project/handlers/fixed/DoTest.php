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
        for ($i = 0; $i < 1000; $i++) {
            echo alpha(shiftBits($i)) . PHP_EOL;
        }
    }
}

function shiftBits($number) {
    $number = $number ^ 0xFF71A72371A7232C;
    for ($i = 0; $i < 64; $i++) {
        $to = ($i * 6) % 64;
        if ((($number >> $i) & 0x01) ^ (($number >> $to) & 0x01)) {
            $number = $number ^ ((1 << $i) + (1 << $to));
        }
    }
    return $number;
}

function alpha($number) {
    $number = abs($number);
    $chars = "ABCDEFGHIJKLMNO-PQRSTUVWXYZabcdefghijklmnopqrstuvwzyz0123456789";
    $result = "";
    do {
        $result .= $chars[($number * 12) % 0x40];
        $number >>= 6;
    } while ($number > 0);
    return $result;
}
