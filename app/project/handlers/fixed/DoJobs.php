<?php

namespace app\project\handlers\fixed;

use app\core\db\builder\SelectQuery;
use app\core\logging\Logger;
use app\core\router\RouteHandler;
use app\libs\WaveformGenerator;
use app\project\models\tracklist\Songs;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;

class DoJobs implements RouteHandler
{
    public function doGet()
    {
        Songs::wipeOldPreviews();

        FileServer::removeUnused();
        FileServer::removeDead();

        $limit = 30;

        while ($limit-- > 0) {
            set_time_limit(30);
            (new SelectQuery(TSongs::_NAME))
                ->select(TSongs::FILE_NAME, TSongs::FILE_ID, TSongs::ID)
                ->where(TSongs::PEAKS_ID . " IS NULL")
                ->where(TSongs::FILE_ID . " IS NOT NULL")
                ->limit(1)
                ->eachRow(function ($row) {
                    Logger::printf("Creating peaks for file: %s", $row[TSongs::FILE_NAME]);
                    $peaks = WaveformGenerator::generate(FileServer::getFileUsingId($row[TSongs::FILE_ID]));
                    $file_id = FileServer::registerByContent(json_encode($peaks), "application/json");
                    SongDao::updateSongUsingId($row[TSongs::ID], [TSongs::PEAKS_ID => $file_id]);
                });
            sleep(1);
        }
    }
}
