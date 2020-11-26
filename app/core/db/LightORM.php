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

    /** @var DatabaseConnection */
    private $db;

    /**
     * @param DatabaseConnection $database
     */
    public function __construct(DatabaseConnection $database) {
        $this->db = $database;
    }

    /**
     * @param $class
     * @throws LightORMException
     */
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

        if (!class_exists($class)) {
            throw new LightORMException($class . " not exists");
        }

        $table_name     = $class_config['$table'];
        $table_key      = $class_config['$key'];
        $table_fields   = $class_config['$fields'];
        $table_crud     = $class_config['$crud'];

        if (!is_string($table_name) || !is_string($table_key) || !is_array($table_fields) || !is_array($table_crud)) {
            throw new LightORMException($class . " is not configured correctly");
        }

        if (!is_string($table_crud['$read'])) {
            throw new LightORMException('READ method is not configured in class ' . $class);
        }

        $graph_data = $this->db
            ->fetchOneRow($this->template($table_crud['$read'], [
                "fields" => join('","', array_keys($table_fields)),
                "table"  => $table_name,
                "key"    => $table_key
            ]), array($id))
            ->getOrThrow(LightORMException::class, "Object " . $class . " with id " . $id . " not exists");

        /** @var PersistentObject */
        $object_instance = new $class;

        $reflection = new \ReflectionClass($object_instance);

        foreach ($graph_data as $key => $value) {
            $prop = $reflection->getProperty($key);
            if (is_null($prop)) {
                throw new LightORMException("Property " . $key . " not found in class " . $class);
            }
            $prop->setAccessible(true);
            $prop->setValue($object_instance, $value);
        }

        $prop = $reflection->getProperty($table_key);
        if (is_null($prop)) {
            throw new LightORMException("Property " . $table_key . " not found in class " . $class);
        }
        $prop->setAccessible(true);
        $prop->setValue($object_instance, $id);

        $prop = $reflection->getProperty("orm");
        $prop->setAccessible(true);
        $prop->setValue($object_instance, $this);

        $prop = $reflection->getProperty("original");
        $prop->setAccessible(true);
        $prop->setValue($object_instance, $graph_data);

        return $object_instance;

    }

    public function save($object) {

        if (!$object instanceof PersistentObject) {
            throw new LightORMException("Object " . get_class($object) . " isn't instance of PersistentObject");
        }

        $class_name = get_class($object);

        $class_config = self::$config[$class_name];

        if (!is_array($class_config)) {
            throw new LightORMException($class_name . " is not configured");
        }

        $table_name     = $class_config['$table'];
        $table_key      = $class_config['$key'];
        $table_fields   = $class_config['$fields'];
        $table_crud     = $class_config['$crud'];

        if (!is_string($table_name) || !is_string($table_key) || !is_array($table_fields) || !is_array($table_crud)) {
            throw new LightORMException($class_name . " is not configured correctly");
        }

        $reflection = new \ReflectionClass($object);

        $key_field = $reflection->getProperty($table_key);
        $key_field->setAccessible(true);
        $id = $key_field->getValue($object);

        $originals_field = $reflection->getProperty("original");
        $originals_field->setAccessible(true);


        if (is_null($id)) {

            if (!is_string($table_crud['$create'])) {
                throw new LightORMException('CREATE method is not configured in class ' . $class_name);
            }

            $data = [];
            foreach ($table_fields as $key => $value) {
                $prop = $reflection->getProperty($key);
                if (is_null($prop)) {
                    throw new LightORMException("Property " . $key . " not found in class " . $class_name);
                }
                $prop->setAccessible(true);
                    $data[$key] = $prop->getValue($object);
            }

            $keys = array_keys($data);
            $values = array_values($data);

            $new_id = $this->db
                ->fetchOneColumn(
                    $this->template($table_crud['$create'], [
                        "table" => $table_name,
                        "fields" => implode('","', $keys),
                        "values" => implode(",", array_fill(0, count($values), "?")),
                        "key" => $table_key
                    ]), $values)
                ->get();

            $key_field->setValue($object, $new_id);

            $originals_field->setValue($object, $data);

        } else {

            if (!is_string($table_crud['$update'])) {
                throw new LightORMException('UPDATE method is not configured in class ' . $class_name);
            }

            $data = [];
            $original = $originals_field->getValue($object);

            foreach ($table_fields as $key => $value) {
                $prop = $reflection->getProperty($key);
                if (is_null($prop)) {
                    throw new LightORMException("Property " . $key . " not found in class " . $class_name);
                }
                $prop->setAccessible(true);
                $prop_value = $prop->getValue($object);
                if ($prop_value != $original[$key]) {
                    $data[$key] = $prop->getValue($object);
                }
            }

            if (count($data) === 0) {
                return;
            }

            $setters = implode(",", array_map(function ($key) { return '"' . $key . '" = ?'; }, array_keys($data)));

            $this->db->executeUpdate(
                $this->template($table_crud['$update'], [
                    "table"     => $table_name,
                    "setters"   => $setters,
                    "key"       => $table_key
                ]), array_merge(array_values($data), array($id)));

        }

    }

    /**
     * @param $class
     * @return PersistentObject
     * @throws LightORMException
     */
    public function create($class) {

        $this->validateClassName($class);

        $class_config = self::$config[$class];

        if (!is_array($class_config)) {
            throw new LightORMException($class . " is not configured");
        }

        if (!class_exists($class)) {
            throw new LightORMException($class . " not exists");
        }

        $table_name     = $class_config['$table'];
        $table_key      = $class_config['$key'];
        $table_fields   = $class_config['$fields'];
        $table_crud     = $class_config['$crud'];

        if (!is_string($table_name) || !is_string($table_key) || !is_array($table_fields) || !is_array($table_crud)) {
            throw new LightORMException($class . " is not configured correctly");
        }

        /** @var PersistentObject */
        $object_instance = new $class;

        $reflection = new \ReflectionClass($object_instance);

        foreach ($table_fields as $key => $value) {
            $prop = $reflection->getProperty($key);
            if (is_null($prop)) {
                throw new LightORMException("Property " . $key . " not found in class " . $class);
            }
            $prop->setAccessible(true);
            $prop->setValue($object_instance, $value);
        }

        $prop = $reflection->getProperty("orm");
        $prop->setAccessible(true);
        $prop->setValue($object_instance, $this);

        return $object_instance;

    }

    /**
     * @param $object
     * @throws LightORMException
     */
    public function delete($object) {

        if (!$object instanceof PersistentObject) {
            throw new LightORMException("Object " . get_class($object) . " isn't instance of PersistentObject");
        }

        $class_name = get_class($object);

        $class_config = self::$config[$class_name];

        if (!is_array($class_config)) {
            throw new LightORMException($class_name . " is not configured");
        }

        $table_name     = $class_config['$table'];
        $table_key      = $class_config['$key'];
        $table_fields   = $class_config['$fields'];
        $table_crud     = $class_config['$crud'];

        if (!is_string($table_name) || !is_string($table_key) || !is_array($table_fields) || !is_array($table_crud)) {
            throw new LightORMException($class_name . " is not configured correctly");
        }

        if (!is_string($table_crud['$delete'])) {
            throw new LightORMException('DELETE method is not configured in class ' . $class_name);
        }

        $reflection = new \ReflectionClass($object);

        $key_field = $reflection->getProperty($table_key);
        $key_field->setAccessible(true);
        $id = $key_field->getValue($object);

        if (is_null($id)) {
            return;
        }

        $this->db->executeUpdate($this->template($table_crud['$delete'], [
            "table" => $table_name,
            "key"   => $table_key
        ]), $id);

        $key_field->setValue($object, null);

    }

    /**
     * @param $template
     * @param array $context
     * @return mixed
     */
    private function template($template, array $context) {
        return preg_replace_callback('/({{\s*(.+?)\s*}})/', function ($match) use ($context) {
            return $this->invoke($context, $match[2]);
        }, $template);
    }

    /**
     * @param $object
     * @param $field
     * @return null
     * @throws \Exception
     */
    private function invoke($object, $field) {
        $path = ml_explode(".", $field);
        $first = array_shift($path);
        if (is_null($first)) {
            return null;
        }
        if (count($path) === 0) {
            if (is_object($object)) {
                $getter = $this->fieldNameToGetter($first);
                if (method_exists($object, $getter)) {
                    return $object->$getter();
                } else if (property_exists($object, $first)) {
                    return $object->$first;
                } else {
                    return null;
                }
            } else if (is_array($object)) {
                return $object[$first];
            } else {
                return null;
            }
        } else {
            return $this->invoke($object[$first], implode(".", $path));
        }
    }

    /**
     * @param $field_name
     * @return string
     * @throws \Exception
     */
    private function fieldNameToGetter($field_name) {
        return 'get' . implode("", array_map("ucfirst", ml_explode('_', $field_name)));
    }

}
