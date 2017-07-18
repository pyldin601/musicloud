<?php

namespace app\project\handlers\fixed\cron;

use app\core\db\builder\SelectQuery;
use app\core\http\HttpServer;
use app\core\logging\Logger;
use app\core\router\RouteHandler;
use app\libs\WaveformGenerator;
use app\project\exceptions\UnauthorizedException;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;
use malkusch\lock\mutex\FlockMutex;

class DoGeneratePeaks implements RouteHandler
{
    public function doPost(HttpServer $server)
    {
        if ($server->getRemoteAddress() !== '127.0.0.1') {
            throw new UnauthorizedException();
        }

        (new FlockMutex(fopen(__FILE__, 'r')))->synchronized(function () {
            SongDao::scopeWithoutPeaks()
                ->eachRow(function ($row) {
                    Logger::printf("Creating peaks for file: %s", $row[TSongs::FILE_NAME]);
                    $peaks = WaveformGenerator::generate(FileServer::getFileUsingId($row[TSongs::FILE_ID]));
                    $file_id = FileServer::registerByContent(json_encode($peaks), "application/json");
                    SongDao::updateSongUsingId($row[TSongs::ID], [TSongs::PEAKS_ID => $file_id]);
                });
        });
    }
}
