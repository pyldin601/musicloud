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
use app\project\persistence\db\tables\UsersTable;

/**
 * Class Auth
 * @package app\project\models
 *
 * Static class for logging in and logging out user
 */
class Auth extends UninstantiatedClass {

    /** @var HttpSession */
    private static $session;

    public static function class_init(HttpSession $session) {
        self::$session = $session;
    }

    /**
     * Login user using $email and $password
     *
     * @param $email
     * @param $password
     * @throws WrongCredentialsException
     */
    public static function login($email, $password) {
        $query = new SelectQuery(UsersTable::TABLE_NAME, UsersTable::EMAIL, $email);
        $user = $query->fetchOneRow()->getOrElse(WrongCredentialsException::class);
        if (password_verify($password, $user[UsersTable::PASSWORD])) {
            self::$session->set($user[UsersTable::ID], "auth", "id");
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