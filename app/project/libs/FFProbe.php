<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 06.05.15
 * Time: 20:21
 */

namespace app\project\libs;


use app\core\etc\Settings;
use app\lang\option\Option;
use app\lang\option\Some;

// todo: fix cp1251 charset support

class FFProbe {

    /** @var Settings */
    private static $settings;

    public static function class_init() {
        self::$settings = resource(Settings::class);
    }

    /**
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

        $json = json_decode(implode("", $result), true);

        $object = new Metadata();

        $object->filename                = @$json["format"]["filename"];
        $object->format_name             = @$json["format"]["format_name"];
        $object->duration                = @doubleval($json["format"]["duration"]);
        $object->size                    = @intval($json["format"]["size"]);
        $object->bitrate                 = @intval($json["format"]["bit_rate"]);

        if (isset($json["format"]["tags"])) {
            $object->meta_artist         = @$json["format"]["tags"]["artist"];
            $object->meta_title          = @$json["format"]["tags"]["title"];
            $object->meta_genre          = @$json["format"]["tags"]["genre"];
            $object->meta_date           = @$json["format"]["tags"]["date"];
            $object->meta_album          = @$json["format"]["tags"]["album"];
            $object->meta_track_number   = @$json["format"]["tags"]["track"];
            $object->meta_album_artist   = @$json["format"]["tags"]["album_artist"];
            $object->is_compilation      = @$json["format"]["tags"]["compilation"];
        }

        return Option::Some($object);

    }

    /**
     * @return mixed
     */
    public function getBitrate() {
        return $this->bitrate;
    }

    /**
     * @return mixed
     */
    public function getDuration() {
        return $this->duration;
    }

    /**
     * @return mixed
     */
    public function getDurationMilliseconds() {
        if ($this->getDuration() === null) {
            return null;
        }
        return (int) ($this->duration * 1000);
    }

    /**
     * @return mixed
     */
    public function getFileName() {
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function getFormatName() {
        return $this->formatName;
    }

    /**
     * @return mixed
     */
    public function getMetaAlbum() {
        return $this->metaAlbum;
    }

    /**
     * @return mixed
     */
    public function getMetaArtist() {
        return $this->metaArtist;
    }

    /**
     * @return mixed
     */
    public function getMetaDate() {
        return $this->metaDate;
    }

    /**
     * @return mixed
     */
    public function getMetaGenre() {
        return $this->metaGenre;
    }

    /**
     * @return mixed
     */
    public function getMetaTitle() {
        return $this->metaTitle;
    }

    /**
     * @return mixed
     */
    public function getMetaTrackNumber() {
        return $this->metaTrackNumber;
    }

    /**
     * @return mixed
     */
    public function getSize() {
        return $this->size;
    }

}