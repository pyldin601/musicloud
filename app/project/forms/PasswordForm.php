<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 17:07
 */

namespace app\project\forms;


use app\abstractions\AbstractForm;

class PasswordForm extends AbstractForm {

    private $password;

    protected function __construct() {
        parent::__construct();
    }

    protected function validate() {

    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

}