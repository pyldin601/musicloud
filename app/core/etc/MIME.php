<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 23.07.2015
 * Time: 11:45
 */

namespace app\core\etc;


class MIME {

    public static function mime_type($file) {

        $mime_type = array(
            "aac" => "audio/x-aac",
            "mp3" => "audio/mpeg",
            "mp4" => "audio/mp4",
            "m4a" => "audio/mpeg",

            "jpg" => "image/jpeg",
            "jpeg"=> "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif"
        );

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (isset($mime_type[$extension])) {

            return $mime_type[$extension];

        } else {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            finfo_close($finfo);

            return $type;

        }

    }
} 