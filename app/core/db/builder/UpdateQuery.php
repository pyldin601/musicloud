<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:42
 */

namespace app\core\db\builder;


use app\lang\Tools;

class UpdateQuery extends BaseQuery {

    use WhereSection;

    private $sets = [];
    private $setsSingle = [];

    function __construct($tableName, $key = null, $value = null) {
        $this->tableName = $tableName;
        if (!Tools::isNull($key, $value)) {
            $this->where($key, $value);
        }
    }

    public function getQuery(\PDO $pdo) {

        $query = [];

        $query[] = "UPDATE " . $this->tableName;
        $query[] = $this->buildSets();
        $query[] = $this->buildWheres($pdo);
        $query[] = $this->buildOrderBy();
        $query[] = $this->buildLimits();

        return implode(" ", $query);

    }

    private function setPair($column, $value) {

        $this->parameters["SET"][] = $value;
        $this->sets[] = $column;

        return $this;

    }

    private function setPairs(array $sets) {
        foreach ($sets as $key => $value) {
            $this->setPair($key, $value);
        }
    }

    private function setSingle($expression) {
        $this->setsSingle[] = $expression;
    }

    public function set() {

        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->setPairs(func_get_arg(0));
        } elseif (func_num_args() == 2 && is_string(func_get_arg(0))) {
            $this->setPair(func_get_arg(0), func_get_arg(1));
        } else if (func_num_args() == 1 && is_string(func_get_arg(0))) {
            $this->setSingle(func_get_arg(0));
        }
        return $this;
    }

    /**
     * @param string $field
     * @param int $amount
     */
    public function increment($field, $amount = 1) {
        $this->setSingle("$field = $field + $amount");
    }

    /**
     * @param $field
     * @param int $amount
     */
    public function decrement($field, $amount = 1) {
        $this->setSingle("$field = $field - $amount");
    }

    public function buildSets() {

        $build = [];

        foreach ($this->sets as $set) {
            $build[] = $set . "=?";
        }

        foreach ($this->setsSingle as $set) {
            $build[] = $set;
        }

        return "SET " . implode(",", $build);

    }

}