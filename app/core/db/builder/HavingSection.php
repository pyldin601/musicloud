<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:39
 */

namespace app\core\db\builder;


trait HavingSection {

    protected $having = [];
    protected $havingGlue = "AND";

    // Where section

    public function having($clause) {
        if (func_num_args() == 2 && is_array(func_get_arg(1))) {
            $this->havingArray(func_get_arg(0), func_get_arg(1));
        } elseif (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->havingHashMap(func_get_arg(0));
        } elseif (func_num_args() == 2) {
            $this->havingSimple(func_get_arg(0), func_get_arg(1));
        } elseif (func_num_args() == 1) {
            $this->havingSimple("(".$clause.")");
        }
        return $this;
    }

    /**
     * @param $column
     * @param $values
     * @return $this
     */
    public function havingFindInSet($column, $values) {
        $this->having[] = "FIND_IN_SET(" . $column . ", ?)";
        $this->parameters["HAVING"][] = $values;
        return $this;
    }


    private function havingSimple($column, $value = null) {
        if (is_null($value)) {
            $this->having[] = $column;
        } else {
            $this->parameters["HAVING"][] = $value;
            $this->having[] = [$column, "?"];
        }
    }

    private function havingParameters($clause, array $parameters) {
        foreach ($parameters as $key => $parameter) {
            if (is_numeric($key)) {
                $this->parameters["HAVING"][] = $parameter;
            } else {
                $this->parameters["HAVING"][$key] = $parameter;
            }
        }
        $this->having[] = $clause;
    }

    private function havingArray($column, array $values) {
        if (preg_match("~(\\?)|(\\:\\w+)~m", $column)) {
            $this->havingParameters($column, $values);
        } else {
            $this->having[] = [$column, $values];
        }
    }

    private function havingHashMap(array $map) {
        foreach ($map as $key => $value) {
            $this->havingSimple($key, $value);
        }
    }


    private function buildHaving(\PDO $pdo) {

        $build = [];

        foreach ($this->having as $having) {
            if (is_string($having)) {
                $build[] = $having;
            } else if (count($having) == 2 && is_array($having[1])) {
                $build[] = $having[0] . " IN (" . implode(",", $this->quote($pdo, $having[1])) . ")";
            } else {
                $build[] = $having[0] . "=" . $having[1];
            }
        }

        return (count($build) > 0 ? "HAVING " . implode(" {$this->havingGlue} ", $build) : "");

    }

    /**
     * @param string $glue
     * @return $this
     */
    public function setHavingGlue($glue) {
        $this->havingGlue = strval($glue);
        return $this;
    }
} 