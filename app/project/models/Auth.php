<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 7/19/15
 * Time: 1:54 PM
 */

namespace app\project\models;


use app\core\db\builder\SelectQuery;
use app\core\helpers\UninstantiatedClass;
use app\core\http\HttpSession;
use app\project\exceptions\WrongCredentialsException;
use app\project\forms\LoginForm;
use app\project\persistence\db\tables\TUsers;

/**
 * Class Auth
 * @package app\project\models
 *
 * Static class for logging in and logging out user
 */
class Auth {

    /** @var HttpSession */
    private static $session;

    public static function class_init() {

        self::$session = resource(HttpSession::class);

    }

    private function __construct() {
        throw new \Exception("This class couldn't be instantiated");
    }

    /**
     * Login user using $email and $password
     *
     * @param LoginForm $form
     * @throws WrongCredentialsException
     */
    public static function login(LoginForm $form) {

        $query = new SelectQuery(TUsers::_NAME, TUsers::EMAIL, $form->getEmail());

        $user = $query->fetchOneRow()->getOrElse(WrongCredentialsException::class);

        if (password_verify($form->getPassword(), $user[TUsers::PASSWORD])) {
            self::$session->set($user[TUsers::ID], "auth", "id");
        } else {
            throw new WrongCredentialsException;
        }
    }

    /**
     * Logout logged in user
     */
    public static function logout() {
        self::$session->erase("auth", "id");
    }

}