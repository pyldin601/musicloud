<?php

use app\lang\option\Option;

/**
 * @param $arg
 * @return Option
 */
function part_audio_type($arg) {

    $formats = array(
        "mp3"  => "audio/mp3",
        "ogg"  => "audio/ogg",
        "flac" => "audio/flac",
        "m4a"  => "audio/aac",
        "aac"  => "audio/aac",
        "ape"  => "audio/ape",
        "wma"  => "audio/x-ms-wma",
        "wav"  => "audio/x-wav"
    );

    return isset($formats[$arg]) ?
        Option::Some($formats[$arg]) :
        Option::None();

};
