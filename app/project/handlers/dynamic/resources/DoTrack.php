<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 25.08.2015
 * Time: 15:43
 */

namespace app\project\handlers\dynamic\resources;


use app\core\db\Database;
use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\persistence\orm\Track;

class DoTrack implements RouteHandler {
    public function doGet(JsonResponse $response, $id, Database $database) {

        $orm = $database->getLightORM();
        $track = $orm->load(Track::class, $id);
        $response->write($track);

    }
} 