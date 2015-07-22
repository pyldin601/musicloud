<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:22
 */

namespace app\project\models;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\lang\Arrays;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\fs\FileServer;

class Tracks {
    /** @var LoggedIn */
    private static $me;
    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    /**
     * @return int Created track id
     */
    public static function create() {
        $query = new InsertQuery(AudiosTable::TABLE_NAME);
        $query->values(AudiosTable::USER_ID, self::$me->getId());
        $query->values(AudiosTable::CREATED_DATE, time());
        return $query->executeInsert();
    }

    public static function delete($track_id) {

        // Explode $track_id string into array of track ids
        $track_ids = explode(",", $track_id);

        // Fetch track objects from database
        $track_objects = (new SelectQuery(AudiosTable::TABLE_NAME))
            ->where(AudiosTable::ID, $track_ids)
            ->fetchAll();

        // Fetch track metadata from database
        $track_metas = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->where(MetadataTable::ID, $track_ids)
            ->whereNotNull(MetadataTable::COVER_FILE_ID)
            ->fetchAll();

        // Check owner to be equal to current user
        $callback = function ($track) {
            return $track[AudiosTable::USER_ID] == self::$me->getId();
        };
        assert(Arrays::all($callback, $track_objects), "One or more of selected tracks is not yours");

        // Remove album covers
        foreach ($track_metas as $track_meta) {
            FileServer::unregister($track_meta[MetadataTable::COVER_FILE_ID]);
        }

        // Remove track files
        foreach ($track_objects as $track_object) {
            FileServer::unregister($track_object[AudiosTable::FILE_ID]);
        }

        // Remove tracks from database
        (new DeleteQuery(AudiosTable::TABLE_NAME))
            ->where(AudiosTable::ID, $track_ids)
            ->update();

    }
}