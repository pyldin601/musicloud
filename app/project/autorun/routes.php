<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\core\http\HttpServer;
use app\project\handlers\dynamic\content\DoReadCover;
use app\project\handlers\dynamic\content\DoReadTrack;


when("content/track/&id", DoReadTrack::class);
when("content/cover/&id", DoReadCover::class);

when("test", function (HttpServer $server) {
    echo $server->getContentType();
});