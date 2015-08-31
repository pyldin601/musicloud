<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:33
 */

namespace app\core\db\builder;


use app\core\db\DatabaseConnection;
use app\lang\MLArray;
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

        $limits = [];

        if (is_numeric($this->limit)) {
            $limits[] = "LIMIT " . $this->limit;
        }

        if (is_numeric($this->offset)) {
            $limits[] = "OFFSET " . $this->offset;
        }

        $result = implode(" ", $limits);

        return $result;

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
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) {
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
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) {
            return $db->fetchOneRow(
                $this->getQuery($db->getPDO()),
                $this->getParameters()
            );
        });
    }

    public function renderAllAsJson($callable = null) {
        DatabaseConnection::doInConnection(function (DatabaseConnection $db) use ($callable) {
            $db->renderAllAsJson($this->getQuery($db->getPDO()), $this->getParameters(), $callable);
        });
    }

    public function writeCSV() {
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) {
            $db->writeCSV(
                $this->getQuery($db->getPDO()),
                $this->getParameters()
            );
        });
    }

    /**
     * @param int $column
     * @return Option
     */
    public function fetchColumn($column = 0) {
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) use (&$column) {
            return $db->fetchOneColumn(
                $this->getQuery($db->getPDO()),
                $this->getParameters(),
                $column
            );
        });
    }

    /**
     * @param string|null $key
     * @param callable $callback
     * @return MLArray
     */
    public function fetchAll($key = null, callable $callback = null) {
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) use (&$key, &$callback) {
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
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) use (&$className, &$ctor_args) {
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
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) use (&$className, &$ctor_args) {
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
        DatabaseConnection::doInConnection(function (DatabaseConnection $db) use (&$callable) {
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
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) {
            return $db->executeUpdate($this->getQuery($db->getPDO()), $this->getParameters());
        });
    }

    /**
     * @return int
     */
    public function executeInsert() {
        return DatabaseConnection::doInConnection(function (DatabaseConnection $db) {
            return $db->executeInsert($this->getQuery($db->getPDO()), $this->getParameters());
        });
    }

} 