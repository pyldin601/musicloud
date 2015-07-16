<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 16.07.15
 * Time: 20:13
 */

namespace app\core\db;


class DatabaseConfiguration {
    private $dsn_uri;
    private $dsn_login;
    private $dsn_password;

    /**
     * @param mixed $dsn_uri
     */
    public function setDsnUri($dsn_uri) {
        $this->dsn_uri = $dsn_uri;
    }

    /**
     * @param mixed $dsn_login
     */
    public function setDsnLogin($dsn_login) {
        $this->dsn_login = $dsn_login;
    }

    /**
     * @param mixed $dsn_password
     */
    public function setDsnPassword($dsn_password) {
        $this->dsn_password = $dsn_password;
    }

    /**
     * @return mixed
     */
    public function getDsnUri() {
        return $this->dsn_uri;
    }

    /**
     * @return mixed
     */
    public function getDsnLogin() {
        return $this->dsn_login;
    }

    /**
     * @return mixed
     */
    public function getDsnPassword() {
        return $this->dsn_password;
    }

}