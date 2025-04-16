<?php

namespace app\project\handlers\dynamic\content;


use app\core\router\RouteHandler;
use app\project\models\tracklist\Song;
use app\project\persistence\db\tables\TSongs;
use app\project\libs\TextSyntheticizer;

class DoGetCommandForAlexa implements RouteHandler
{
    public function doGet($id)
    {
        $track = new Song($id);
        $artist = $track->getObject()[TSongs::T_ARTIST];
        $title = $track->getObject()[TSongs::T_TITLE];

        $command = "Alexa! Play ${title} by ${artist} on Spotify!";

        TextSyntheticizer::say(command: $command);
    }
}