<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 9:30
 */


use app\core\cache\TempFileProvider;
use app\core\etc\Settings;
use app\core\injector\Injector;
use app\libs\WaveformGenerator;

Injector::run(function (Settings $settings) {

    WaveformGenerator::setCommand($settings->get("tools", "ffmpeg_cmd"));
    TempFileProvider::setTempPath($settings->get("fs", "temp"));

});


