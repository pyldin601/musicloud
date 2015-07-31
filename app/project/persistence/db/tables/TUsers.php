<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:25
 */

namespace app\project\persistence\db\tables;


class TUsers {

    const _NAME                 = "users";

    const ID                    = "id";
    const EMAIL                 = "email";
    const PASSWORD              = "password";

    const ID_FULL               = self::_NAME . "." . self::ID;
    const EMAIL_FULL            = self::_NAME . "." . self::EMAIL;
    const PASSWORD_FULL         = self::_NAME . "." . self::PASSWORD;

}