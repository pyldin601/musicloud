<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:39
 */

namespace app\core\db\builder;


class InsertQuery extends BaseQuery {

    protected $names = [];
    protected $duplicate = [];
    protected $duplicateSingle = [];
    protected $returning = null;

    /**
     * @param $tableName
     */
    function __construct($tableName) {
        $this->tableName = $tableName;
    }

    public function values() {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->hashValues(func_get_arg(0));
        } elseif (func_num_args() == 2 && is_string(func_get_arg(0))) {
            $this->singleValue(func_get_arg(0), func_get_arg(1));
        }
        return $this;
    }

    private function singleValue($key, $value) {
        $this->names[] = $key;
        $this->parameters["INSERT"][] = $value;
    }

    private function hashValues(array $hashMap) {
        foreach ($hashMap as $key => $value) {
            $this->singleValue($key, $value);
        }
    }

    private function buildDuplicates() {

        $build = [];

        foreach ($this->duplicate as $set) {
            $build[] = $set . "=?";
        }

        foreach ($this->duplicateSingle as $set) {
            $build[] = $set;
        }

        return "ON DUPLICATE KEY UPDATE " . implode(",", $build);

    }

    private function updatePair($column, $value) {

        $this->parameters["UPDATE"][] = $value;
        $this->duplicate[] = $column;

        return $this;

    }

    private function updatePairs(array $sets) {
        foreach ($sets as $key => $value) {
            $this->updatePair($key, $value);
        }
    }

    private function setSingle($expression) {
        $this->duplicateSingle[] = $expression;
    }

    public function set() {

        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->updatePairs(func_get_arg(0));
        } elseif (func_num_args() == 2 && is_string(func_get_arg(0))) {
            $this->updatePair(func_get_arg(0), func_get_arg(1));
        } else if (func_num_args() == 1 && is_string(func_get_arg(0))) {
            $this->setSingle(func_get_arg(0));
        }
        return $this;
    }

    protected function groupNames() {

        return implode(",", $this->names);

    }

    public function returning($id) {
        $this->returning = $id;
        return $this;
    }

    public function getParameters() {
        return array_merge($this->parameters["INSERT"], $this->parameters["UPDATE"]);
    }

    public function getQuery(\PDO $pdo) {

        $query = [];

        $query[] = "INSERT INTO";
        $query[] = $this->tableName;
        $query[] = "(" . $this->groupNames() . ")";
        $query[] = "VALUES";
        $query[] = "(" . $this->repeat('?', count($this->parameters["INSERT"]), ',') . ")";

        if (count($this->duplicate) + count($this->duplicateSingle) > 0) {
            $query[] = $this->buildDuplicates();
        }

        if ($this->returning !== null) {
            $query[] = "RETURNING " . $this->returning;
        }

        return implode(" ", $query);

    }
} 