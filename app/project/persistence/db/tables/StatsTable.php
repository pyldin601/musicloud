<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 12:14
 */

namespace app\project\persistence\db\tables;


class StatsTable {
    const TABLE_NAME = "track_stats";
    const ID = "id";
    const PLAYBACKS = "playbacks_count";
    const SKIPS = "skips_count";
    const LAST_PLAYED_DATE = "last_played_date";
} 