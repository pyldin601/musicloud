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
use app\core\exceptions\ApplicationException;
use app\lang\Arrays;
use app\lang\Tools;
use app\project\models\single\LoggedIn;
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

    /**
     * @return string Created track id
     * @throws ApplicationException
     */
    public static function create() {

        $max_retries = 1000;

        do {

            $key = Tools::generateRandomKey();
            $query = new InsertQuery(TSongs::_NAME);
            $query->values(TSongs::ID, $key);
            $query->values(TSongs::USER_ID, self::$me->getId());

            try {

                $result = $query->executeInsert();

            } catch (\PDOException $exception) {

                $key = null;
                $result = null;

            }

        } while ($result === null && $max_retries-- > 0);

        if ($key === null) {
            throw new ApplicationException("Database couldn't generate unique id!");
        }

        return $key;

    }

    public static function delete($track_id) {

        assert(strlen($track_id) > 0, "At lease one track id must be specified");

        // Explode $track_id string into array of track ids
        $track_ids = explode(",", $track_id);

        // Fetch track objects from database
        $track_objects = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::ID, $track_ids)
            ->fetchAll();

        // Check owner to be equal to current user
        $callback = function ($track) {
            return $track[TSongs::USER_ID] == self::$me->getId();
        };

        assert(Arrays::all($callback, $track_objects), "One or more of selected tracks is not yours");

        // Unregister file links
        foreach ($track_objects as $track) {
            if ($track[TSongs::C_BIG_ID])
                FileServer::unregister($track[TSongs::C_BIG_ID]);
            if ($track[TSongs::C_MID_ID])
                FileServer::unregister($track[TSongs::C_MID_ID]);
            if ($track[TSongs::C_SMALL_ID])
                FileServer::unregister($track[TSongs::C_SMALL_ID]);
            if ($track[TSongs::FILE_ID])
                FileServer::unregister($track[TSongs::FILE_ID]);
        }

        // Remove tracks from database
        (new DeleteQuery(AudiosTable::TABLE_NAME))
            ->where(AudiosTable::ID, $track_ids)
            ->update();

    }
}