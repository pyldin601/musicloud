<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 05.08.2015
 * Time: 11:52
 */

namespace app\project\persistence\db\dao;


use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\project\persistence\db\tables\TSongs;

class SongDao {
    const UNUSED_THRESHOLD = 2592000;
    const UNUSED_PER_REQUEST_MAX = 10;

    /**
     * @param $song_id
     * @return mixed
     */
    public static function getSongUsingId($song_id) {
        return (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::ID, $song_id)
            ->fetchOneRow()
            ->getOrThrow(DaoException::class, sprintf("Song by id %s not found", $song_id));
    }

    public static function updateSongUsingId($song_id, array $sets) {
        (new UpdateQuery(TSongs::_NAME, TSongs::ID, $song_id))
            ->set($sets)->update();
    }

    /**
     * @param $track_id
     * @param $field_name
     */
    public static function incrementUsingId($track_id, $field_name) {
        (new UpdateQuery(TSongs::_NAME, TSongs::ID, $track_id))
            ->increment($field_name)
            ->update();
    }

    /**
     * @return array
     */
    public static function getListOfUnusedPreviews() {
        return (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::PREVIEW_ID . " IS NOT NULL")
            ->where(TSongs::LP_DATE . "< EXTRACT(EPOCH FROM NOW()) - " . self::UNUSED_THRESHOLD)
            ->limit(self::UNUSED_PER_REQUEST_MAX)
            ->fetchAll();
    }

    public static function getList(array $criteria = null) {
        return (new SelectQuery(TSongs::_NAME))
            ->where($criteria)
            ->fetchAll();
    }

    public static function each(array $criteria = null, $callback) {
        (new SelectQuery(TSongs::_NAME))
            ->where($criteria)
            ->eachRow($callback);
    }

} 