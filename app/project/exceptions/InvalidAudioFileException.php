<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 23.07.2015
 * Time: 11:26
 */

namespace app\project\exceptions;


use app\core\http\HttpStatusCodes;

class InvalidAudioFileException extends UploadException {
    public function __construct() {
        parent::__construct("Audio file could not be read", HttpStatusCodes::HTTP_BAD_REQUEST);
    }
} 