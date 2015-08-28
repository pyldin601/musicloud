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
use app\lang\MLArray;
use app\lang\option\Mapper;
use app\project\libs\FFProbe;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;

class Songs {

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

        $track_ids = in_string(",", $track_id) ? explode(",", $track_id) : array($track_id);

        self::validateListOfTrackIds($track_ids);

        $track_objects = self::getTracksListById($track_ids);

        $isTrackBelongsToUser = function ($track) {
            return $track[TSongs::USER_ID] == self::$me->getId();
        };

        if (!$track_objects->all($isTrackBelongsToUser)) {
            throw new ControllerException("One or more selected tracks is not belongs to you");
        }

        foreach ($track_objects as $track) {
            Logger::printf("Removing track %s", $track[TSongs::ID]);
            self::removeFilesUsedByTrack($track);
        }

        self::deleteTracksById($track_ids);

    }

    private static function getTracksListById($id) {
        return (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, self::$me->getId())
            ->where(TSongs::ID, $id)
            ->fetchAll();
    }

    private static function deleteTracksById($id) {
        (new DeleteQuery(TSongs::_NAME))
            ->where(TSongs::USER_ID, self::$me->getId())
            ->where(TSongs::ID, $id)
            ->update();
    }

    private static function removeFilesUsedByTrack($track) {

        if ($track[TSongs::C_BIG_ID]) {
            FileServer::unregister($track[TSongs::C_BIG_ID]);
        }

        if ($track[TSongs::C_MID_ID]) {
            FileServer::unregister($track[TSongs::C_MID_ID]);
        }

        if ($track[TSongs::C_SMALL_ID]) {
            FileServer::unregister($track[TSongs::C_SMALL_ID]);
        }

        if ($track[TSongs::PREVIEW_ID]) {
            FileServer::unregister($track[TSongs::PREVIEW_ID]);
        }

        if ($track[TSongs::PEAKS_ID]) {
            FileServer::unregister($track[TSongs::PEAKS_ID]);
        }

        if ($track[TSongs::FILE_ID]) {
            FileServer::unregister($track[TSongs::FILE_ID]);
        }

    }

    public static function wipeOldPreviews() {
        foreach (SongDao::getListOfUnusedPreviews() as $song) {
            Logger::printf("Wiping old track preview (file id %s)", $song[TSongs::PREVIEW_ID]);
            FileServer::unregister($song[TSongs::PREVIEW_ID]);
            SongDao::updateSongUsingId($song[TSongs::ID], [TSongs::PREVIEW_ID => null]);
        }
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

    /**
     * @param $song_id
     * @param array $metadata
     * @return array
     */
    public static function edit($song_id, array $metadata) {

        $allowed_keys = [
            TSongs::T_TITLE, TSongs::T_ARTIST, TSongs::T_ALBUM, TSongs::A_ARTIST,
            TSongs::IS_COMP, TSongs::T_GENRE, TSongs::T_YEAR,
            TSongs::T_NUMBER, TSongs::DISC
        ];

        $numeric_keys = [TSongs::T_NUMBER, TSongs::DISC];

        $filtered_metadata = array_intersect_key($metadata, array_flip($allowed_keys));

        $query = (new UpdateQuery(TSongs::_NAME))
            ->where(TSongs::ID, explode(",", $song_id))
            ->where(TSongs::USER_ID, self::$me->getId());

        foreach ($filtered_metadata as $key => $value) {
            if (in_array($key, $numeric_keys)) {
                $query->set($key, is_numeric($value) ? $value : null);
            } else {
                $query->set($key, $value);
            }
        }

        $query->returning(TSongs::defaultSelection());

        return $query->fetchAll();
    }

    public static function removeCover($song_id) {

        $song_ids = explode(",", $song_id);

        $song_objects = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::ID, $song_ids)
            ->where(TSongs::USER_ID, self::$me->getId())
            ->fetchAll();

        foreach ($song_objects as $song) {

            if ($song[TSongs::C_SMALL_ID]) {
                FileServer::unregister($song[TSongs::C_SMALL_ID]);
            }
            if ($song[TSongs::C_MID_ID]) {
                FileServer::unregister($song[TSongs::C_MID_ID]);
            }
            if ($song[TSongs::C_BIG_ID]) {
                FileServer::unregister($song[TSongs::C_BIG_ID]);
            }

        }

        $query = (new UpdateQuery(TSongs::_NAME))
            ->where(TSongs::ID, $song_ids)
            ->where(TSongs::USER_ID, self::$me->getId())
            ->set(TSongs::C_SMALL_ID, null)
            ->set(TSongs::C_MID_ID, null)
            ->set(TSongs::C_BIG_ID, null)
            ->returning(implode(",", [
                TSongs::ID, TSongs::C_SMALL_ID,
                TSongs::C_MID_ID, TSongs::C_BIG_ID
            ]));

        return $query->fetchAll();

    }

    public static function changeCover($song_id, $cover_file) {

        $song_ids = explode(",", $song_id);

        $song_objects = (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::ID, $song_ids)
            ->where(TSongs::USER_ID, self::$me->getId())
            ->fetchAll();

        // Delete exists covers
        foreach ($song_objects as $song) {

            if ($song[TSongs::C_SMALL_ID]) {
                FileServer::unregister($song[TSongs::C_SMALL_ID]);
            }
            if ($song[TSongs::C_MID_ID]) {
                FileServer::unregister($song[TSongs::C_MID_ID]);
            }
            if ($song[TSongs::C_BIG_ID]) {
                FileServer::unregister($song[TSongs::C_BIG_ID]);
            }

        }

        $covers = FFProbe::readTempCovers($cover_file)
            ->getOrThrow(ControllerException::class, "Image file is corrupted");

        $query = (new UpdateQuery(TSongs::_NAME))
            ->where(TSongs::ID, $song_ids)
            ->where(TSongs::USER_ID, self::$me->getId());

        $full_cover_id = FileServer::register($covers[0]);
        $middle_cover_id = FileServer::register($covers[1]);
        $small_cover_id = FileServer::register($covers[2]);

        $query  ->set(TSongs::C_SMALL_ID, $small_cover_id)
                ->set(TSongs::C_MID_ID, $middle_cover_id)
                ->set(TSongs::C_BIG_ID, $full_cover_id);

        $query->returning(implode(",", [
            TSongs::ID, TSongs::C_SMALL_ID,
            TSongs::C_MID_ID, TSongs::C_BIG_ID
        ]));

        return $query->fetchAll();

    }

    public static function checkRights($track_id) {

    }

    /**
     * @param $track_artist
     * @return MLArray
     */
    public static function deleteByArtist($track_artist) {

        $songs = SongDao::getList([
            TSongs::T_ARTIST => $track_artist,
            TSongs::USER_ID => self::$me->getId()
        ]);

        $to_remove = $songs->map(Mapper::key(TSongs::ID));

        if (count($to_remove) == 0) {
            return array();
        }

        return (new DeleteQuery(TSongs::_NAME))
            ->where(TSongs::ID, $to_remove->mkArray())
            ->returning("*")
            ->fetchAll();

    }


}