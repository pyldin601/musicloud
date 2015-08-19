<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 16:40
 */

namespace app\project\handlers\fixed\api\playlist;


use app\core\router\RouteHandler;
use app\core\view\JsonResponse;
use app\project\forms\NewPlaylistForm;
use app\project\models\Playlist;

class DoCreate implements RouteHandler {
    public function doPost(JsonResponse $response, NewPlaylistForm $form) {
        $response->write(Playlist::create($form->getName()));
    }
} 