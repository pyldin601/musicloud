<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 28.07.2015
 * Time: 9:26
 */

namespace app\libs;


use app\core\cache\TempFileProvider;
use app\core\exceptions\ApplicationException;

class WaveformGenerator {
    const PEAKS_RESOLUTION = 4096;
    const READ_BUFFER_SIZE = 2048;

    private static $ffmpeg_cmd;
    public static function setCommand($ffmpeg) {
        self::$ffmpeg_cmd = $ffmpeg;
    }

    /**
     * @param $file_name
     * @return array
     * @throws ApplicationException
     */
    public static function generate($file_name) {

        $temp_file = TempFileProvider::generate("peaks", ".raw");
        $command = sprintf("%s -v quiet -i %s -ac 1 -f u8 -ar 44100 -acodec pcm_u8 %s",
            self::$ffmpeg_cmd, escapeshellarg($file_name), escapeshellarg($temp_file));

        shell_exec($command);

        if (!file_exists($temp_file)) {
            throw new ApplicationException("Waveform could not be generated!");
        }

        $chunk_size = ceil(filesize($temp_file) / self::PEAKS_RESOLUTION);

        $peaks = withOpenedFile($temp_file, "r", function ($fh) use (&$chunk_size) {
            while ($data = read($fh, $chunk_size)) {
                $peak = 0;
                for ($i = 0; $i < strlen($data); $i ++) {
                    $code = ord(substr($data, $i, 1)) - 127;
                    if ($code > $peak) {
                        $peak = $code;
                    }
                }
                yield $peak;
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