<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 14:43
 */

namespace app\project\models\single;


use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\project\exceptions\IncorrectPasswordException;
use app\project\exceptions\UserNotFoundException;
use app\project\persistence\db\tables\TUsers;

class User {

    private $user;
    private $user_id;

    /**
     * @param int $user_id
     */
    public function __construct($user_id) {
        $query = new SelectQuery(TUsers::_NAME, TUsers::ID, $user_id);
        $this->user = $query->fetchOneRow()->getOrThrow(UserNotFoundException::class);
        $this->user_id = $user_id;
    }

    /**
     * @param string $new_password
     */
    public function changePassword($new_password) {
        $query = new UpdateQuery(TUsers::_NAME, TUsers::ID, $this->user_id);
        $query->set(TUsers::PASSWORD, password_hash($new_password, PASSWORD_DEFAULT));
    }

    /**
     * @param string $password
     * @throws IncorrectPasswordException
     */
    public function verifyPassword($password) {
        $result = password_verify($password, $this->user[TUsers::PASSWORD]);
        if (! $result) {
            throw new IncorrectPasswordException;
        }
    }

    public function __toString() {
        return "User({$this->user[TUsers::EMAIL]})";
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->user[TUsers::ID];
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->user[TUsers::EMAIL];
    }

} 