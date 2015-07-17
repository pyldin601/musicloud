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
use app\core\http\HttpSession;
use app\project\exceptions\WrongCredentialsException;
use app\project\persistence\db\tables\Users;

class UsersModel {

    /** @var HttpSession */
    private static $session;

    public static function static_init() {
        self::$session = HttpSession::getInstance();
    }

    /**
     * @param $email
     * @param $password
     */
    public static function create($email, $password) {
        $query = new InsertQuery(Users::TABLE_NAME);
        $query->values(Users::EMAIL, $email);
        $query->values(Users::PASSWORD, password_hash($password, PASSWORD_DEFAULT));
        $query->executeInsert();
    }

    /**
     * @param $id
     */
    public static function delete($id) {
        $query = new DeleteQuery(Users::TABLE_NAME, Users::ID, $id);
        $query->update();
    }

    /**
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

    public static function logout() {
        self::$session->erase("auth", "id");
    }

}