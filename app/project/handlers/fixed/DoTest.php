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
        foreach (range(0, 10000, 1) as $i) {
            echo letterify(shiftBits($i)) . PHP_EOL;
        }
    }
}
/*
 * 0 1 => 1
 * 1 0 => 1
 * 1 1 => 0
 * 0 0 => 0
 */
function shiftBits($number) {
    $inv = [2, 3, 4, 7, 8, 9, 10, 13, 15, 17, 18, 19, 21, 23, 26, 28, 29, 31];
    $swp = [30, 21, 31, 21, 27, 31, 11, 17, 0, 25, 16, 24, 13, 12, 26, 15, 28, 30,
        18, 7, 19, 23, 4, 6, 3, 5, 9, 20, 29, 22, 1, 14];
    foreach ($inv as $n) {
        $number = $number ^ (1 << $n);
    }
    foreach ($swp as $from => $to) {
        $from_state = (($number >> $from) & 0x1);
        $to_state = (($number >> $to) & 0x1);
        if ($from_state ^ $to_state) {
            $number = $number ^ ((1 << $from) + (1 << $to));
        }
    }
    return $number;
}

function letterify($number) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwzyz0123456789";
    $len = strlen($chars);
    $result = "";
    do {
        $result .= $chars[$number % $len];
        $number /= $len;
    } while ($number > 1);
    return $result;
}
