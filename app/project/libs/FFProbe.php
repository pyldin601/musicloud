<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 06.05.15
 * Time: 20:21
 */

namespace app\project\libs;


use app\core\cache\TempFileProvider;
use app\core\etc\Settings;
use app\lang\option\Option;
use app\lang\option\Some;

class FFProbe {

    /** @var Settings */
    private static $settings;

    public static function class_init() {
        self::$settings = resource(Settings::class);
    }

    /**
     * Reads album cover from audio file if possible.
     *
     * @param $filename
     * @return Option
     */
    public static function readTempCovers($filename) {

        $escaped_filename = escapeshellarg($filename);

        $temp_cover_full   = TempFileProvider::generate("cover", ".jpeg");
        $temp_cover_middle = TempFileProvider::generate("cover", ".jpeg");
        $temp_cover_small  = TempFileProvider::generate("cover", ".jpeg");

        $command = sprintf("%s -i %s -v quiet -an -vf \"scale='min(iw,900)':-1\" %s -vf \"scale='min(iw,450)':-1\" %s -vf \"scale='min(iw,250)':-1\" %s",
            self::$settings->get("tools", "ffmpeg_cmd"), $escaped_filename,
            $temp_cover_full, $temp_cover_middle, $temp_cover_small);

        exec($command, $result, $status);

        if (file_exists($temp_cover_full)) {
            return Option::Some([$temp_cover_full, $temp_cover_middle, $temp_cover_small]);
        } else {
            return Option::None();
        }

    }

    /**
     * Reads audio file metadata and returns Metadata object.
     *
     * @param string $filename
     * @return Some
     */
    public static function read($filename) {

        $escaped_filename = escapeshellarg($filename);

        $command = sprintf("%s -i %s -v quiet -print_format json -show_format",
            self::$settings->get("tools", "ffprobe_cmd"), $escaped_filename);

        exec($command, $result, $status);

        if ($status != 0) {
            return Option::None();
        }

        /**
         * @var Option[] $metadata
         * @var Option[] $o_format
         * @var Option[] $o_tags
         */

        $metadata = Option::Some(json_decode(implode("", $result), true));

        $o_format = $metadata["format"];
        $o_tags = $o_format["tags"];

        $object = new Metadata();

        $object->filename           = $o_format["filename"]     ->get();
        $object->format_name        = $o_format["format_name"]  ->get();
        $object->duration           = $o_format["duration"]     ->map("doubleval")->get();
        $object->size               = $o_format["size"]         ->toInt()->get();
        $object->bitrate            = $o_format["bit_rate"]     ->toInt()->get();

        $object->meta_artist        = $o_tags["artist"]         ->orEmpty();
        $object->meta_title         = $o_tags["title"]          ->orEmpty();
        $object->meta_genre         = $o_tags["genre"]          ->orEmpty();
        $object->meta_date          = $o_tags["date"]           ->orEmpty();
        $object->meta_album         = $o_tags["album"]          ->orEmpty();
        $object->meta_track_number  = $o_tags["track"]          ->toInt()->orNull();
        $object->meta_disc_number   = $o_tags["disc"]           ->toInt()->orNull();
        $object->meta_album_artist  = $o_tags["album_artist"]   ->orEmpty();
        $object->is_compilation     = $o_tags["compilation"]    ->map("bool")->getOrElse("false");
        $object->meta_comment       = $o_tags["comment"]        ->orEmpty();

        return Option::Some($object);

    }

    /**
     * Converts text from CP1251 to UTF-8 encoding.
     *
     * @param $chars
     * @return string
     */
    static function cp1251dec($chars) {

        $test = @iconv("UTF-8", "CP1252", $chars);

        if (is_null($test)) {
            return $chars;
        } else {
            return iconv("CP1251", "UTF-8", $test);
        }

    }

}