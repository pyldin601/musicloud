<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:05
 */

namespace app\project\models\single;


use app\core\http\HttpSession;
use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;
use app\project\exceptions\UnauthorizedException;

class LoggedIn extends User implements SingletonInterface, Injectable {

    use Singleton;

    public function __construct() {
        $logged_in = HttpSession::getInstance()->get("auth", "id")
            ->getOrThrow(UnauthorizedException::class);
        parent::__construct($logged_in);
    }


} 