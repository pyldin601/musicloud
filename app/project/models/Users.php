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
use app\project\forms\RegistrationForm;
use app\project\persistence\db\tables\TUsers;

class Users
{


    /**
     * Creates user with specified $email and $password
     *
     * @param RegistrationForm $form
     * @throws EmailExistsException
     */
    public static function create(RegistrationForm $form) {
        $query = new InsertQuery(TUsers::_NAME);
        $query->values(TUsers::EMAIL, $form->getEmail());
        $query->values(TUsers::PASSWORD, password_hash($form->getPassword(), PASSWORD_DEFAULT));
        $query->values(TUsers::NAME, $form->getName());

        $query->executeInsert();
    }

    /**
     * Changes $password for user with specified $email
     *
     * @param $email
     * @param $password
     */
    public static function changePassword($email, $password) {
        $query = new UpdateQuery(TUsers::_NAME);
        $query->where(TUsers::EMAIL, $email);
        $query->set(TUsers::PASSWORD, password_hash($password, PASSWORD_DEFAULT));
    }

    /**
     * Deletes user from database
     *
     * @param $user_id
     */
    public static function delete($user_id) {
        $query = new DeleteQuery(TUsers::_NAME, TUsers::ID, $user_id);
        $query->update();
    }

    /**
     * Returns true if email is used
     * @param $email
     * @return bool
     */
    public static function checkEmailConstraint($email) {
        return (new SelectQuery(TUsers::_NAME, TUsers::EMAIL, $email))
            ->fetchOneRow()->nonEmpty();
    }

}