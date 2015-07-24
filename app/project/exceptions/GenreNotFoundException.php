<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 24.07.2015
 * Time: 15:30
 */

namespace app\project\exceptions;


use app\core\http\HttpStatusCodes;

class GenreNotFoundException extends BackendException {
    public function __construct() {
        parent::__construct("Genre does not exists in your library", HttpStatusCodes::HTTP_NOT_FOUND);
    }
} 