<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:33
 */

namespace app\core\db\builder;


use app\core\db\Database;
use app\lang\option\Option;

abstract class BaseQuery implements QueryBuilder {

    protected $tableName;

    protected $orders = [];

    protected $parameters = [
        "SET" => [],
        "WHERE" => [],
        "INSERT" => [],
        "UPDATE" => [],
        "HAVING" => []
    ];

    protected $limit = null;
    protected $offset = null;

    protected function quoteColumnName($column) {
        return "`" . $column . "`";
    }

    protected function repeat($char, $count, $glue = "") {
        $chars = [];
        for ($i = 0; $i < $count; $i++) {
            $chars[] = $char;
        }
        return implode($glue, $chars);
    }

    protected function quote(\PDO $pdo, array $values) {
        $result = [];
        foreach ($values as $value) {
            $result[] = $pdo->quote($value);
        }
        return $result;
    }

    abstract function getQuery(\PDO $pdo);

    public function getParameters() {
        return array_merge($this->parameters["INSERT"], $this->parameters["SET"],
            $this->parameters["WHERE"], $this->parameters["HAVING"]);
    }

    public function orderBy($column) {
        if ($column === null) {
            $this->orders = [];
        } else {
            $this->orders[] = $column;
        }
        return $this;
    }

    public function buildLimits() {

        if (is_numeric($this->limit) && is_null($this->offset)) {
            return "LIMIT " . $this->limit;
        } else if (is_numeric($this->limit) && is_numeric($this->offset)) {
            return "LIMIT " . $this->offset . "," . $this->limit;
        } else {
            return "";
        }

    }


    protected function buildOrderBy() {

        if (count($this->orders) > 0) {
            return "ORDER BY " . implode(", ", $this->orders);
        } else {
            return "";
        }

    }

    /* Fetchers shortcuts */

    /**
     * @return string
     */
    public function generate() {
        return Database::doInConnection(function (Database $db) {
            /** @var SelectQuery $query */
            return $db->generate(
                $this->getQuery($db->getPDO()),
                $this->getParameters()
            );
        });
    }

    /**
     * @return Option
     */
    public function fetchOneRow() {
        return Database::doInConnection(function (Database $db) {
            /** @var SelectQuery $query */
            $query = clone $this;
            $query->limit(1);

            return $db->fetchOneRow(
                $query->getQuery($db->getPDO()),
                $query->getParameters()
            );
        });
    }

    /**
     * @param int $column
     * @return Option
     */
    public function fetchOneColumn($column = 0) {
        return Database::doInConnection(function (Database $db) use (&$column) {
            /** @var SelectQuery $query */
            $query = clone $this;
            $query->limit(1);

            return $db->fetchOneColumn(
                $query->getQuery($db->getPDO()),
                $query->getParameters(),
                $column
            );
        });
    }

    /**
     * @param string|null $key
     * @param callable $callback
     * @return array
     */
    public function fetchAll($key = null, callable $callback = null) {
        return Database::doInConnection(function (Database $db) use (&$key, &$callback) {
            return $db->fetchAll(
                $this->getQuery($db->getPDO()),
                $this->getParameters(),
                $key,
                $callback
            );
        });
    }

    /**
     * @param $className
     * @param array $ctor_args
     * @return Option
     */
    public function fetchObject($className, array $ctor_args = []) {
        return Database::doInConnection(function (Database $db) use (&$className, &$ctor_args) {
            /** @var SelectQuery $query */
            $query = clone $this;
            $query->limit(1);

            return $db->fetchOneObject(
                $query->getQuery($db->getPDO()),
                $query->getParameters(),
                $className,
                $ctor_args
            );
        });
    }

    /**
     * @param $className
     * @param array $ctor_args
     * @return Object[]
     */
    public function fetchAllObjects($className, array $ctor_args = []) {
        return Database::doInConnection(function (Database $db) use (&$className, &$ctor_args) {
            return $db->fetchAllObjects(
                $this->getQuery($db->getPDO()),
                $this->getParameters(),
                $className,
                $ctor_args
            );
        });
    }

    /**
     * @param callable $callable
     */
    public function eachRow(callable $callable) {
        Database::doInConnection(function (Database $db) use (&$callable) {
            $db->eachRow(
                $this->getQuery($db->getPDO()),
                $this->getParameters(),
                $callable
            );
        });
    }

    /**
     * @return mixed
     */
    public function update() {
        return Database::doInConnection(function (Database $db) {
            return $db->executeUpdate($this->getQuery($db->getPDO()), $this->getParameters());
        });
    }

    /**
     * @return int
     */
    public function executeInsert() {
        return Database::doInConnection(function (Database $db) {
            return $db->executeInsert($this->getQuery($db->getPDO()), $this->getParameters());
        });
    }

} 