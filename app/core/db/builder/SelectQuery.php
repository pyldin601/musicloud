<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:41
 */

namespace app\core\db\builder;


use app\core\db\Database;
use app\core\exceptions\ApplicationException;
use app\lang\Tools;

class SelectQuery extends BaseQuery implements \Countable {

    use WhereSection, SelectSection, HavingSection;


    protected $groups = [];

    private $innerJoin = [];
    private $leftJoin = [];

    public function __construct($tableName, $key = null, $value = null) {
        $this->tableName = $tableName;
        if (!Tools::isNull($key, $value)) {
            $this->where($key, $value);
        }
    }

    /**
     * @return int
     */
    public function count() {
        return Database::doInConnection(function (Database $db) use (&$className, &$ctor_args) {
            $query = clone $this;
            $query->selectNone()->selCount();
            $query->limit(null);
            $query->offset(null);
            $query->orderBy(null);
            return intval($db->fetchOneColumn(
                $query->getQuery($db->getPDO()),
                $query->getParameters()
            )->get());
        });
    }

    /**
     * @param $chunk_size
     * @param $callback
     */
    public function chunk($chunk_size, $callback) {
        $items = $this->fetchAll();
        $chunks = array_chunk($items, $chunk_size);
        while ($chunk = array_shift($chunks)) {
            call_user_func($callback, $chunk);
        }
    }

    // Inner join builder section

    public function innerJoin($other_table, $other_column, $this_column) {

        $this->innerJoin[] = [$other_table, " ON " . $other_column . " = " . $this_column];

        return $this;

    }

    public function joinUsing($other_table, $key) {

        $this->innerJoin[] = [$other_table, " USING (" . $key . ")"];

        return $this;

    }

    // Left join builder section

    public function leftJoin($table, $other_column, $this_column) {

        $this->leftJoin[] = [$table, " ON " . $other_column . " = " . $this_column];

        return $this;

    }

    /**
     * @param int $limit
     * @throws ApplicationException
     * @return $this
     */
    public function limit($limit) {
        if ($limit !== null && !is_numeric($limit) && $limit < 0) {
            throw new ApplicationException("Invalid limit");
        }
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @throws ApplicationException
     * @return $this
     */
    public function offset($offset) {
        if ($offset !== null && !is_numeric($offset) && $offset < 0) {
            throw new ApplicationException("Invalid offset");
        }
        $this->offset = $offset;
        return $this;
    }

    // Implements

    public function getQuery(\PDO $pdo) {

        $query = [];

        $query[] = "SELECT SQL_NO_CACHE";
        $query[] = $this->buildSelect();
        $query[] = "FROM " . $this->tableName;
        $query[] = $this->buildInnerJoins();
        $query[] = $this->buildLeftJoins();
        $query[] = $this->buildWheres($pdo);
        $query[] = $this->buildGroupBy();
        $query[] = $this->buildHaving($pdo);
        $query[] = $this->buildOrderBy();
        $query[] = $this->buildLimits();

        return implode(" ", $query);

    }

    private function buildInnerJoins() {

        $build = [];

        foreach ($this->innerJoin as $join) {
            $build[] = "INNER JOIN " . $join[0] . $join[1];
        }

        return implode(" ", $build);

    }

    private function buildLeftJoins() {

        $build = [];

        foreach ($this->leftJoin as $join) {
            $build[] = "LEFT JOIN " . $join[0] . $join[1];
        }

        return implode(" ", $build);

    }


    protected function buildGroupBy() {

        if (count($this->groups) > 0) {
            return "GROUP BY " . implode(", ", $this->groups);
        } else {
            return "";
        }

    }


    /**
     * @param string $column
     * @return $this
     */
    public function addGroupBy($column) {
        $this->groups[] = $column;
        return $this;
    }

}