<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 14:36
 */

namespace app\project\models;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\http\HttpSession;
use app\project\exceptions\EmailExistsException;
use app\project\exceptions\WrongCredentialsException;
use app\project\persistence\db\tables\Users;

class UsersModel {

    /** @var HttpSession */
    private static $session;

    public static function class_init(HttpSession $session) {
        self::$session = $session;
    }

    /**
     * Test whether $email already registered
     *
     * @param $email
     * @return bool
     */
    public static function isEmailExists($email) {
        return (new SelectQuery(Users::TABLE_NAME, Users::EMAIL, $email))
            ->fetchOneRow()->nonEmpty();
    }

    /**
     * Creates user with specified $email and $password
     *
     * @param $email
     * @param $password
     * @throws EmailExistsException
     */
    public static function create($email, $password) {
        $query = new InsertQuery(Users::TABLE_NAME);
        $query->values(Users::EMAIL, $email);
        $query->values(Users::PASSWORD, password_hash($password, PASSWORD_DEFAULT));
        try {
            $query->executeInsert();
        } catch (\PDOException $exception) {
            throw new EmailExistsException;
        }
    }

    /**
     * Changes $password for user with specified $email
     *
     * @param $email
     * @param $password
     */
    public static function changePassword($email, $password) {
        $query = new UpdateQuery(Users::TABLE_NAME);
        $query->where(Users::EMAIL, $email);
        $query->set(Users::PASSWORD, password_hash($password, PASSWORD_DEFAULT));
    }

    /**
     * Deletes user from database
     *
     * @param $user_id
     */
    public static function delete($user_id) {
        $query = new DeleteQuery(Users::TABLE_NAME, Users::ID, $user_id);
        $query->update();
    }

    /**
     * Login user using $email and $password
     *
     * @param $email
     * @param $password
     * @throws WrongCredentialsException
     */
    public static function login($email, $password) {
        $query = new SelectQuery(Users::TABLE_NAME, Users::EMAIL, $email);
        $user = $query->fetchOneRow()->getOrElse(WrongCredentialsException::class);
        if (password_verify($password, $user[Users::PASSWORD])) {
            self::$session->set($user[Users::ID], "auth", "id");
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