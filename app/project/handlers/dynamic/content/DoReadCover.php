<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 21.07.15
 * Time: 21:22
 */

namespace app\project\handlers\dynamic\content;


use app\core\db\builder\SelectQuery;
use app\core\exceptions\status\PageNotFoundException;
use app\core\router\RouteHandler;
use app\project\exceptions\BadAccessException;
use app\project\models\single\LoggedIn;
use app\project\models\tracklist\Track;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\fs\FileServer;

class DoReadCover implements RouteHandler {
    public function doGet($id, LoggedIn $me) {

        (new SelectQuery(MetadataTable::TABLE_NAME, MetadataTable::COVER_FILE_ID_FULL, $id))
            ->fetchOneRow()
            ->orThrow(PageNotFoundException::class)
            ->filter(function ($row) use ($me) { return $row[MetadataTable::USER_ID] === $me->getId(); })
            ->orThrow(BadAccessException::class);

        FileServer::writeToClient($id);

    }
}