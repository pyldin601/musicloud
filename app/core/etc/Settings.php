<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 11:49
 */

namespace app\core\etc;


use app\core\injector\Injectable;
use app\lang\option\Option;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;

class Settings implements SingletonInterface, Injectable {

    use Singleton;

    private $_settings;

    protected function __construct() {
        $this->_settings = parse_ini_file(INI_FILE_NAME, true);
    }

    /**
     * @param string $section
     * @param string $key
     * @return mixed
     */
    public function get($section, $key) {
        return @$this->_settings[$section][$key];
    }

    /**
     * @param string $section
     * @param string $key
     * @return Option
     */
    public function find($section, $key) {
        if (isset($this->_settings[$section][$key])) {
            return Option::Some($this->_settings[$section][$key]);
        }
        return Option::None();
    }

}