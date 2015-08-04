<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 04.08.2015
 * Time: 9:16
 */

namespace app\core\db\pgsimple;


class Select {
    private $fields;
    public function __construct($_) {
        if (is_array($_)) {
            $this->fields = $_;
        } else {
            $this->fields = func_get_args();
        }
    }
} 