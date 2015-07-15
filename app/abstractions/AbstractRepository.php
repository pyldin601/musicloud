<?php

namespace app\abstractions;


use app\lang\option\Option;

abstract class AbstractRepository {

    /**
     * @param string $key
     * @return bool
     */
    public abstract function isDefined($key);

    /**
     * @param string $key
     * @return mixed
     */
    protected abstract function getValue($key);

    /**
     * @param $key
     * @return \Exception
     */
    protected abstract function getException($key);

    /**
     * @param string $key
     * @return Option
     */
    public function get($key) {
        return ($this->isDefined($key))
            ? Option::Some($this->getValue($key))
            : Option::None();
    }

    /**
     * @param $key
     * @param $alt
     * @return mixed
     */
    public function getOrElse($key, $alt = null) {
        return ($this->isDefined($key)) ? $this->getValue($key) : $alt;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     * @throws mixed
     */
    public function getOrError($key) {

        if ($this->isDefined($key)) {
            return $this->getValue($key);
        } else {
            $this->raiseError($key);
        }

        return null;

    }

    /**
     * @param $key
     * @throws \Exception
     */
    private function raiseError($key) {
        throw $this->getException($key);
    }

}
