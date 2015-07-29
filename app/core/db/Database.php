<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 9:53
 */

namespace app\core\db;


use app\core\cache\RedisCache;
use app\core\etc\Settings;
use app\core\exceptions\ApplicationException;
use app\core\injector\Injectable;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;
use PDO;

class Database implements SingletonInterface, Injectable {

    use Singleton;

    /** @var PDO $pdo */
    private $pdo;
    /** @var DatabaseConfiguration $configuration */
    private static $configuration;

    protected function __construct() {

        $this->connect();

    }

    public static function configure(DatabaseConfiguration $configuration) {
        self::$configuration = $configuration;
    }

    /**
     * @return $this
     * @throws ApplicationException
     */
    private function connect() {

        if (self::$configuration === null) {
            throw new ApplicationException("Database not configured");
        }

        $pdo_dsn = self::$configuration->getDsnUri();
        $pdo_login = self::$configuration->getDsnLogin();
        $pdo_password = self::$configuration->getDsnPassword();
        $pdo_options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_AUTOCOMMIT => true,
//            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );

        $this->pdo = new PDO($pdo_dsn, $pdo_login, $pdo_password, $pdo_options);

        return $this;

    }

    /**
     * @param callable $callable
     * @return mixed
     */
    public static function doInConnection(callable $callable) {

        $conn = self::getInstance();
        return $conn->doInTransaction($callable);

    }

    /**
     * @param callable(Database) $callable
     * @return mixed
     */
    public function doInTransaction(callable $callable) {

        return call_user_func($callable, $this);

    }

    /**
     * @return PDO
     */
    public function getPDO() {
        return $this->pdo;
    }

    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollBack();
    }

    public function finishTransaction() {
        return $this->pdo->rollBack();
    }

    /**
     * @param string $query
     * @param array $params
     * @return string
     */
    public function queryQuote($query, $params = []) {

        $position = 0;

        $arguments = preg_replace_callback("/(\\?)|(\\:\\w+)/", function ($match) use ($params, &$position) {
            $array_key = $match[0] === '?' ? $position++ : $match[0];
            if (!isset($params[$array_key])) {
                return 'NULL';
            }
            return is_numeric($params[$array_key])
                ? $params[$array_key]
                : $this->pdo->quote($params[$array_key],
                    PDO::PARAM_STR);
        }, $query);


        return $arguments;

    }

    /**
     * @param $query
     * @param $params
     * @throws ApplicationException
     * @return \PDOStatement
     */
    private function createResource($query, $params = null) {

        $queryString = $this->queryQuote($query, $params);

//        error_log($queryString);

        $resource = $this->pdo->prepare($queryString);

        if ($resource === false) {
//            error_log(sprintf("ERROR : %s", $queryString));
            throw new ApplicationException($this->pdo->errorInfo()[2]);
        }

//        $begin = microtime(true);
        $resource->execute();
//        $end = microtime(true);

//        error_log(sprintf("%0.4f : %s", $end - $begin, $queryString));


        if ($resource->errorCode() !== "00000") {
            throw new ApplicationException($resource->errorInfo()[2]);
        }

        return $resource;

    }

    /**
     * @param string $query
     * @param array $params
     * @param string $key
     * @param Callable $callback
     * @return array
     */
    public function fetchAll($query, array $params = null, $key = null, callable $callback = null) {

        $resource = $this->createResource($query, $params);
        $db_result = $resource->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($db_result as $i => $row) {

            if (is_callable($callback)) {
                $row = call_user_func_array($callback, [$row, $i]);
            }

            if (!is_null($key)) {
                $k = $row[$key];
                unset($row[$key]);
                $result[$k] = $row;
            } else {
                $result[] = $row;
            }

        }


        return $result;

    }

    /**
     * @param $query
     * @param array $params
     * @param callable $callback
     */
    public function eachRow($query, array $params = null, callable $callback) {

        $resource = $this->createResource($query, $params);

        while ($row = $resource->fetch(PDO::FETCH_ASSOC)) {
            call_user_func($callback, $row);
            unset($row);
        }

        $resource->closeCursor();

    }

    /**
     * @param string $query
     * @param array $params
     * @param Callable $callback
     * @return Option
     */
    public function fetchOneRow($query, array $params = null, $callback = null) {

        $resource = $this->createResource($query, $params);

        $row = $resource->fetch(PDO::FETCH_ASSOC);

        if ($row !== false && is_callable($callback)) {
            $row = call_user_func($callback, $row);
        }

        return Option::Some($row)->reject(false);

    }

    /**
     * @param string $query
     * @param array $params
     * @param int $column
     * @return Option
     */
    public function fetchOneColumn($query, array $params = null, $column = 0) {

        $resource = $this->createResource($query, $params);

        $row = $resource->fetchColumn($column);

        if (is_numeric($row)) {
            $row = intval($row);
        }

        return Option::Some($row)->reject(false);

    }

    /**
     * @param string $query
     * @param array $params
     * @param string $class
     * @param array|null $ctr_args
     * @throws ApplicationException
     * @return Option
     */
    public function fetchOneObject($query, array $params = null, $class, array $ctr_args = []) {

        $resource = $this->createResource($query, $params);

        $object = $resource->fetchObject($class, $ctr_args);

        return Option::Some($object)->reject(false);

    }

    /**
     * @param string $query
     * @param array|null $params
     * @param $class
     * @param array|null $ctr_args
     * @return array
     */
    public function fetchAllObjects($query, array $params = null, $class, array $ctr_args = null) {

        $resource = $this->createResource($query, $params);

        $objects = $resource->fetchAll(PDO::FETCH_CLASS, $class, $ctr_args);

        return $objects;

    }

    /**
     * @param string $query
     * @param array|null $params
     * @return int
     */
    public function executeUpdate($query, array $params = null) {

        $resource = $this->createResource($query, $params);

        return $resource->rowCount();

    }

    /**
     * @param string $query
     * @param array|null $params
     * @return int
     */
    public function executeInsert($query, array $params = null) {

        $this->createResource($query, $params);

        return intval($this->pdo->lastInsertId(null));

    }

    /**
     * @param string $query
     * @param array $params
     */
    public function justExecute($query, array $params = null) {

        $this->createResource($query, $params)->closeCursor();

    }

    public function quote($var) {

        return $this->pdo->quote($var, PDO::PARAM_STR);

    }

    /**
     * @param $query
     * @param $params
     * @return string
     */
    public function generate($query, $params) {

        return $this->queryQuote($query, $params);

    }

} 