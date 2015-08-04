<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 04.08.2015
 * Time: 9:06
 */

namespace app\core\db\pgsimple;


class Where implements QueryPart {
    private $field;
    private $operator;
    private $value;
    private $args;
    public function __construct($field, $op, $value, array $args = null) {
        $this->field = $field;
        $this->operator = $op;
        $this->value = $value;
        $this->args = $args;
    }
    public function __toString() {
        return "WHERE(".$this->field.$this->operator.$this->value.")";
    }
    function getQuery(\PDO $pdo) {
        return $this->field.$this->operator.$pdo->quote($this->value);
    }
    function getParameters() {
        return $this->args;
    }
}