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

    private function validateClassName($class) {
        if (!isset(self::$config[$class])) {
            throw new LightORMException("Class " . $class . " is not mapped to any database tables");
        }
        if (!(new \ReflectionClass($class))->implementsInterface(PersistentObject::class)) {
            throw new LightORMException("Class " . $class . " is not instance of PersistentObject");
        }
    }

    /**
     * @param $class
     * @param $id
     * @return PersistentObject
     * @throws LightORMException
     */
    public function load($class, $id) {

        $this->validateClassName($class);

        $class_config = self::$config[$class];

        if (!is_array($class_config)) {
            throw new LightORMException($class . " is not configured");
        }

        $table_name     = $class_config['$table'];
        $table_key      = $class_config['$key'];
        $table_fields   = $class_config['$fields'];

        if (!is_string($table_name) || !is_string($table_key) || !is_array($table_fields)) {
            throw new LightORMException($class . " is not configured correctly");
        }

        $graph_data = $this->db
            ->fetchOneRow(
                sprintf("SELECT %s FROM %s WHERE %s = ?", join(",", array_keys($table_fields)), $table_name, $table_key),
                array($id)
            )
            ->getOrThrow(LightORMException::class, "Object " . $class . " with id " . $id . " not exists");

        /** @var PersistentObject */
        $object_instance = new $class;

        $reflection = new \ReflectionClass($object_instance);

        foreach ($graph_data as $key => $value) {
            $property = $reflection->getProperty($key);
            if (is_null($property)) {
                throw new LightORMException("Property " . $key . " not found in class " . $class);
            }
            $property->setAccessible(true);
            $property->setValue($object_instance, $value);
        }

        return $object_instance;

    }

    public function save($class) {

    }

    public function create($class) {

    }

    public function delete($class) {

    }

}