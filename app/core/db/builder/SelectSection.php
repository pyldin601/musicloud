<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:41
 */

namespace app\core\db\builder;


trait SelectSection {

    protected $selects = [];

    // Select builder section

    public function select($column = null) {

        if ($column === null) {
            $this->selectNone();
        } else if (func_num_args() == 1 && is_string(func_get_arg(0))) {
            $this->addSelect(func_get_arg(0));
        } else if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->addSelectArray(func_get_arg(0));
        } else {
            $this->addSelectArray(func_get_args());
        }

        return $this;

    }

    public function selectNone() {
        $this->selects = [];
        return $this;
    }

    public function selCount() {
        return $this->select("COUNT(*)");
    }

    private function addSelectArray(array $array) {
        foreach ($array as $column) {
            $this->addSelect($column);
        }
    }

    private function addSelect($column) {
        $this->selects[] = $column;
    }

    public function selectAlias($column, $alias) {
        $this->selects[] = [$column, $alias];
        return $this;
    }

    private function selectAll() {
        $this->selects[] = "*";
    }

    // Builders

    private function buildSelect() {

        $build = [];

        foreach ($this->selects as $select) {
            $build[] = is_array($select) ? $select[0] . " AS " . $select[1] : $select;
        }

        return count($build) == 0 ? "*" : implode(",", $build);

    }


} 