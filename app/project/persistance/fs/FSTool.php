<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 9:34
 */

namespace app\project\persistance\fs;


use app\core\etc\Settings;

class FSTool {
    public static function hashToPath($hash) {
        $prefix = Settings::getInstance()->get("fs", "media");
        return sprintf("%s/%s/%s", $prefix, substr($hash, 0, 2), substr($hash, 2, 2));
    }
} 