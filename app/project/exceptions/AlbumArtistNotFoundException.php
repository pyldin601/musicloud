<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 24.07.2015
 * Time: 14:23
 */

namespace app\project\exceptions;


use app\core\http\HttpStatusCodes;

class AlbumArtistNotFoundException extends BackendException {
    public function __construct() {
        parent::__construct("Album artist does not exists in your library", HttpStatusCodes::HTTP_NOT_FOUND);
    }
}