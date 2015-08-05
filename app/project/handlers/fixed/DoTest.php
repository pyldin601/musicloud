<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 01.08.15
 * Time: 00:14
 */

namespace app\project\handlers\fixed;


use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\exceptions\ApplicationException;
use app\core\http\HttpGet;
use app\core\router\RouteHandler;
use app\project\libs\FFProbe;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;
use app\project\persistence\fs\FSTools;

class DoTest implements RouteHandler {
    public function doGet(HttpGet $httpGet) {
        header("Content-Type: text/plain");
        (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::FORMAT . " IS NULL")
            ->where(TSongs::FILE_ID . " IS NOT NULL")
            ->eachRow(function ($row) {
                $file_path = FileServer::getFileUsingId($row[TSongs::FILE_ID]);
                $metadata = FFProbe::read($file_path);
                if ($metadata->nonEmpty()) {
                    $format_name = explode(",", $metadata->get()->format_name)[0];
                    (new UpdateQuery(TSongs::_NAME, TSongs::ID, $row[TSongs::ID]))
                        ->set(TSongs::FORMAT, $format_name)
                        ->update();
                }
            });
    }
}

