<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 24.07.2015
 * Time: 15:26
 */

namespace app\project\exceptions;


use app\core\http\HttpStatusCodes;

class AlbumNotFoundException extends BackendException {
    public function __construct() {
        parent::__construct("Album does not exists in your library", HttpStatusCodes::HTTP_NOT_FOUND);
    }

} 