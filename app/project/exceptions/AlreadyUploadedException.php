<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 23.07.2015
 * Time: 11:15
 */

namespace app\project\exceptions;


use app\core\http\HttpStatusCodes;

class AlreadyUploadedException extends UploadException {
    public function __construct() {
        parent::__construct("File already uploaded", HttpStatusCodes::HTTP_CONFLICT);
    }
} 