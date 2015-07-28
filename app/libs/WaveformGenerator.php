<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 9:26
 */

namespace app\libs;


class WaveformGenerator {
    private static $ffmpeg_cmd;
    public static function setCommand($ffmpeg) {
        self::$ffmpeg_cmd = $ffmpeg;
    }

    /**
     * @param $file_name
     * @return array
     */
    public static function generate($file_name) {

        $file_name_esc = escapeshellarg($file_name);
        $command = sprintf("%s -v quiet -i %s -ac 1 -ar 128 -f u8 -acodec pcm_u8 -",
            self::$ffmpeg_cmd, $file_name_esc);

        $data_raw       = shell_exec($command);
        $data_ord       = array_map("ord", $data_raw);
        $data_lowered   = array_map(self::decrement(128), $data_ord);
        $data_positive  = array_values(array_filter($data_lowered, self::isPositive()));
        $data_chunks    = array_chunk($data_positive, 16);

        $result = array_map("max", $data_chunks);

        return $result;

    }

    /**
     * @param $amount
     * @return callable
     */
    private static function decrement($amount) {
        return function ($value) use ($amount) {
            return $value - $amount;
        };
    }

    /**
     * @return callable
     */
    public static function isPositive() {
        return function ($value) { return $value >= 0; };
    }
} 