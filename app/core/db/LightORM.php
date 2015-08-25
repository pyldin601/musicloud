<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 25.08.15
 * Time: 20:08
 */

namespace app\core\db;


class LightORM {

    private static $config;

    public static function setup($config) {
        self::$config = $config;
    }

    /** @var Database */
    private $db;

    public function __construct(Database $database) {
        $this->db = $database;
    }

    public function load($class, $id) {
        $class_config = self::$config[$class];
        if (!is_array($class_config)) {
            throw new \Exception($class . " is not configured");
        }
        $table_name = $class_config['$table'];
        $table_key = $class_config['$key'];
        $table_fields = $class_config['$fields'];
        if (is_null($table_name) || is_null($table_key) || is_null($table_fields)) {
            throw new \Exception($class . " is not configured correctly");
        }
        $object = $this->db->fetchOneRow(sprintf("SELECT %s FROM %s WHERE %s = ?",
            implode(",", $table_fields), $table_name, $table_key), array($id));
    }

    public function save($class) {

    }

    public function create($class) {

    }

    public function delete($class) {

    }

}