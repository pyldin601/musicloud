<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 17.08.2015
 * Time: 16:15
 */

namespace app\project\forms;


use app\core\exceptions\ValidatorException;
use app\lang\option\Option;
use app\project\models\Users;

class RegistrationForm extends LoginForm {

    /** @var Option */
    protected $name;

    private $_name;

    protected function validate() {

        parent::validate();

        $this->_name = $this->name->orEmpty();

        if (Users::checkEmailConstraint($this->_email)) {
            throw new ValidatorException("User with this email already registered");
        }

    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->_name;
    }

}