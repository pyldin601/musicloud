<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 15:05
 */

namespace app\project\models\single;


use app\core\http\HttpServer;
use app\core\http\HttpSession;
use app\core\injector\Injectable;
use app\lang\singleton\Singleton;
use app\lang\singleton\SingletonInterface;
use app\project\exceptions\UnauthorizedException;
use app\project\exceptions\UserNotFoundException;

class LoggedIn extends User implements SingletonInterface, Injectable {

    use Singleton;

    public function __construct() {
        $request = HttpServer::getInstance();
        if ($request->getRemoteAddress() == $request->getServerAddress()) {
            $logged_in = 0;
        } else {
            $logged_in = HttpSession::getInstance()->get("auth", "id")
                ->getOrThrow(UnauthorizedException::class);
        }
        parent::__construct($logged_in);
    }

    public static function isLoggedIn() {

        try {

            new self();

            return true;

        } catch (UnauthorizedException $ex) {

        } catch (UserNotFoundException $ex) {

        }

        return false;

    }

} 