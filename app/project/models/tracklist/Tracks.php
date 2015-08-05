<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:22
 */

namespace app\project\models\tracklist;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\exceptions\ApplicationException;
use app\core\exceptions\ControllerException;
use app\core\logging\Logger;
use app\lang\Arrays;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;

class Tracks {

    /** @var LoggedIn */
    private static $me;

    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    public static function create() {

        $query = new InsertQuery(TSongs::_NAME);
        $query->values(TSongs::USER_ID, self::$me->getId());
        $query->returning(TSongs::ID);
        $returnedKey = $query->fetchColumn()->get();

        return $returnedKey;

    }

    public static function delete($track_id) {

        if (empty($track_id)) {
            throw new ControllerException("At lease one track id must be specified");
        }

        $track_ids = in_string(",", $track_id) ? explode(",", $track_id) : $track_id;

        self::validateListOfTrackIds($track_ids);

        $track_objects = self::getTracksListById($track_ids);

        $isTrackBelongsToUser = function ($track) {
            return $track[TSongs::USER_ID] == self::$me->getId();
        };

        if (!Arrays::allMatches($isTrackBelongsToUser, $track_objects)) {
            throw new ControllerException("One or more selected tracks is not belongs to you");
        }

        foreach ($track_objects as $track) {
            self::removeFilesUsedByTrack($track);
        }

        self::deleteTracksById($track_ids);

    }

    private static function getTracksListById($id) {
        return (new SelectQuery(TSongs::_NAME))->where(TSongs::ID, $id)->fetchAll();
    }

    private static function deleteTracksById($id) {
        (new DeleteQuery(TSongs::_NAME))->where(TSongs::ID, $id)->update();
    }

    private static function removeFilesUsedByTrack($track) {

        if ($track[TSongs::C_BIG_ID])
            FileServer::unregister($track[TSongs::C_BIG_ID]);

        if ($track[TSongs::C_MID_ID])
            FileServer::unregister($track[TSongs::C_MID_ID]);

        if ($track[TSongs::C_SMALL_ID])
            FileServer::unregister($track[TSongs::C_SMALL_ID]);

        if ($track[TSongs::PREVIEW_ID])
            FileServer::unregister($track[TSongs::PREVIEW_ID]);

        if ($track[TSongs::FILE_ID])
            FileServer::unregister($track[TSongs::FILE_ID]);

    }

    public static function wipeOldPreviews() {
        SongDao::buildQueryForUnusedPreviews()->eachRow(function ($row) {
                Logger::printf("Wiping old track preview (file id %s)", $row[TSongs::PREVIEW_ID]);
            FileServer::unregister($row[TSongs::PREVIEW_ID]);
               SongDao::unsetPreviewUsingId($row[TSongs::ID]);
        });
    }

    private static function validateListOfTrackIds($track_ids) {
        switch (gettype($track_ids)) {
            case "string":
                if (is_empty($track_ids))
                    throw new ControllerException("Incorrect file id(s) specified");
                break;
            case "array":
                if (Arrays::any("is_empty", $track_ids))
                    throw new ControllerException("Incorrect file id(s) specified");
                break;
            default:
                throw new ApplicationException("Incorrect type of argument");
        }
    }

}