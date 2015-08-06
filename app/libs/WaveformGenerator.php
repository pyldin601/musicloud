<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 9:26
 */

namespace app\libs;


use app\core\cache\TempFileProvider;

class WaveformGenerator {
    const PEAKS_RESOLUTION = 1600;

    private static $ffmpeg_cmd;
    public static function setCommand($ffmpeg) {
        self::$ffmpeg_cmd = $ffmpeg;
    }

    /**
     * @param $file_name
     * @return array
     */
    public static function generate($file_name) {

        $temp_file = TempFileProvider::generate("peaks", ".raw");
        $command = sprintf("%s -v quiet -i %s -ac 1 -ar 256 -f u8 -acodec pcm_u8 %s",
            self::$ffmpeg_cmd, escapeshellarg($file_name), escapeshellarg($temp_file));

        shell_exec($command);

        if (!file_exists($temp_file)) {
            return null;
        }

        $chunk_size = floor(filesize($temp_file) / self::PEAKS_RESOLUTION);

        $peaks = withOpenedFile($temp_file, "r", function ($fh) use (&$chunk_size) {
            while ($data = fread($fh, $chunk_size)) {
                $bytes_array    = str_split($data);
                $codes_array    = array_map("ord", $bytes_array);
                $lowered_array  = array_map(self::decrement(128), $codes_array);
                $absolute_array = array_map("abs", $lowered_array);
                yield max($absolute_array);
            }
        });

        TempFileProvider::delete($temp_file);

        return $peaks;

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
    private static function isPositive() {
        return function ($value) { return $value >= 0; };
    }
} 