<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:58
 */

namespace app\project\exceptions;


class TrackNotFoundException extends BackendException {
    public function __construct() {
        parent::__construct("Track not found", 404);
    }
} 