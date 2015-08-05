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

    /**
     * @return SelectQuery
     */
    public static function buildQueryForUnusedPreviews() {
        return (new SelectQuery(TSongs::_NAME))
            ->where(TSongs::PREVIEW_ID . " IS NOT NULL")
            ->where(TSongs::LP_DATE . "< extract(epoch from now()) - " . self::UNUSED_THRESHOLD)
            ->limit(10);
    }

    /**
     * @param $track_id
     */
    public static function unsetPreviewUsingId($track_id) {
        (new UpdateQuery(TSongs::_NAME))
            ->where(TSongs::ID, $track_id)
            ->set(TSongs::PREVIEW_ID, null)
            ->update();
    }
} 