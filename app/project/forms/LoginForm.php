<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:55
 */

namespace app\project\forms;


use app\abstractions\AbstractForm;
use app\core\exceptions\ValidatorException;
use app\lang\option\Filter;
use app\lang\option\Option;

class LoginForm extends AbstractForm {

    /** @var Option */
    protected $email, $password;

    protected $_email, $_password;

    public function __construct() {
        parent::__construct();
    }

    protected function validate() {
        $this->_email = $this->email
            ->orThrow(ValidatorException::class, "You must specify your email")
            ->filter(Filter::matchRegExp(VALIDATOR_EMAIL_TEMPLATE))
            ->getOrThrow(ValidatorException::class, "You must specify correct email");

        $this->_password = $this->password
            ->orThrow(ValidatorException::class, "You must specify your password")
            ->filter(Filter::lengthInRange(VALIDATOR_PWD_MIN_LENGTH, VALIDATOR_PWD_MAX_LENGTH))
            ->getOrThrow(ValidatorException::class,
                sprintf("Password must be in range from %d to %d chars",
                VALIDATOR_PWD_MIN_LENGTH, VALIDATOR_PWD_MAX_LENGTH)
            );
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->_email;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->_password;
    }

}