<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 03.07.2015
 * Time: 9:44
 */

namespace app\core\exceptions\status;


use app\core\exceptions\StatusException;
use app\core\http\HttpStatusCodes;

class PageNotFoundException extends StatusException {
    public function __construct() {
        parent::__construct("Document Not Found", HttpStatusCodes::HTTP_NOT_FOUND);
    }

}