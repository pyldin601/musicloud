<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 7/17/17
 * Time: 17:16
 */

namespace app\project\handlers\fixed\cron;


use app\core\http\HttpServer;
use app\core\router\RouteHandler;
use app\project\exceptions\UnauthorizedException;
use app\project\persistence\fs\FileServer;
use malkusch\lock\mutex\FlockMutex;

class DoCleanFileSystem implements RouteHandler
{
    public function doPost(HttpServer $server)
    {
        if ($server->getRemoteAddress() !== '127.0.0.1') {
            throw new UnauthorizedException();
        }

        (new FlockMutex(fopen(__FILE__, 'r')))->synchronized(function () {
            FileServer::removeUnused();
            FileServer::removeDead();
        });
    }
}
