<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 16.08.15
 * Time: 21:21
 */

namespace app\lang;


abstract class Collection {
    private $keys;
    private $values;

    public function writeJSON() {
        header("Content-Type: application/json; charset=utf8");
        echo '{';
        echo '"k":';
        echo json_encode($this->keys, JSON_UNESCAPED_UNICODE);
        echo ',';
        echo '"v":';
        echo '';
    }
}