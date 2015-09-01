<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 01.09.2015
 * Time: 9:55
 */

namespace app\project\persistence\db\tables;


class TSongsLog {
    const _NAME         = "songs_log";

    const ID            = "id";
    const USER_ID       = "user_id";
    const SONG_ID       = "song_id";
    const ACTION        = "action";

    const ACTION_ADD    = "add";
    const ACTION_DEL    = "delete";
    const ACTION_UPDATE = "update";
} 