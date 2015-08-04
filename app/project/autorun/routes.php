<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\lang\Tools;
use app\project\handlers\dynamic\catalog;
use app\project\handlers\dynamic\content;


when("preview/:id", content\DoGetPreview::class);
when("file/:id", content\DoGetFile::class);


