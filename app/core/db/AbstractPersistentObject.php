<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 25.08.15
 * Time: 23:47
 */

namespace app\core\db;


abstract class AbstractPersistentObject implements PersistentObject, \JsonSerializable {

    /** @var LightORM */
    protected $orm;

    protected $original;

    public function jsonSerialize() {
        $fields = [];
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getMethods() as $method) {
            if (substr($method->getName(), 0, 3) !== 'get') continue;
            if (in_string("@JsonIgnore", $method->getDocComment())) continue;
            $underscore = preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($method->getName(), 3));
            $fields[strtolower($underscore)] = $method->invoke($this);
        }
        return $fields;
    }

    public function save() {
        $this->orm->save($this);
    }

    public function delete() {
        $this->orm->delete($this);
    }

    /**
     * @return LightORM
     * @JsonIgnore
     */
    public function getLightORM() {
        return $this->orm;
    }

}