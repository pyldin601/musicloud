<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:55
 */

namespace app\project\forms;


use app\abstractions\AbstractForm;

class LoginForm extends AbstractForm {

    protected $email, $password, $save;

    public function __construct() {
        parent::__construct();
    }

    protected function validate() { }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getSave() {
        return $this->save;
    }

}