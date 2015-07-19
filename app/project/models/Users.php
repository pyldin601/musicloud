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
use app\project\exceptions\EmailExistsException;
use app\project\persistence\db\tables\UsersTable;

class Users
{

    /**
     * Test whether user with email $email already registered
     *
     * @param $email
     * @return bool
     */
    public static function isEmailExists($email)
    {
        return (new SelectQuery(UsersTable::TABLE_NAME, UsersTable::EMAIL, $email))
            ->fetchOneRow()->nonEmpty();
    }

    /**
     * Creates user with specified $email and $password
     *
     * @param $email
     * @param $password
     * @throws EmailExistsException
     */
    public static function create($email, $password)
    {
        $query = new InsertQuery(UsersTable::TABLE_NAME);
        $query->values(UsersTable::EMAIL, $email);
        $query->values(UsersTable::PASSWORD, password_hash($password, PASSWORD_DEFAULT));
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
    public static function changePassword($email, $password)
    {
        $query = new UpdateQuery(UsersTable::TABLE_NAME);
        $query->where(UsersTable::EMAIL, $email);
        $query->set(UsersTable::PASSWORD, password_hash($password, PASSWORD_DEFAULT));
    }

    /**
     * Deletes user from database
     *
     * @param $user_id
     */
    public static function delete($user_id)
    {
        $query = new DeleteQuery(UsersTable::TABLE_NAME, UsersTable::ID, $user_id);
        $query->update();
    }

}