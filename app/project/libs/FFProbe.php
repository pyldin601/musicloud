<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 06.05.15
 * Time: 20:21
 */

namespace app\project\libs;


use app\core\etc\Settings;

class FFProbe {

    private $filename = null;
    private $formatName = null;
    private $duration = null;
    private $size = null;
    private $bitrate = null;
    private $metaArtist = null;
    private $metaTitle = null;
    private $metaGenre = null;
    private $metaDate = null;
    private $metaAlbum = null;
    private $metaTrackNumber = null;

    /** @var Settings */
    private static $settings;

    public static function class_init() {
        self::$settings = resource(Settings::class);
    }

    /**
     * @param string $filename
     * @return FFprobe|null
     */
    public static function read($filename) {

        if (! file_exists($filename)) {
            error_log("File {$filename} doesn't exists!");
            return null;
        }

        $escaped_filename = escapeshellarg($filename);

        $command = sprintf("%s -i %s -v quiet -print_format json -show_format",
            self::$settings->get("tools", "ffprobe_cmd"), $escaped_filename);

        exec($command, $result, $status);

        if ($status != 0) {
            return null;
        }

        $json = json_decode(implode("", $result), true);

        $object = new self();

        $object->filename               = @$json["format"]["filename"];
        $object->formatName             = @$json["format"]["format_name"];
        $object->duration               = intval(@$json["format"]["duration"]);
        $object->size                   = intval(@$json["format"]["size"]);
        $object->bitrate                = intval(@$json["format"]["bit_rate"]);

        if (isset($json["format"]["tags"])) {
            $object->metaArtist         = @$json["format"]["tags"]["artist"];
            $object->metaTitle          = @$json["format"]["tags"]["title"];
            $object->metaGenre          = @$json["format"]["tags"]["genre"];
            $object->metaDate           = @$json["format"]["tags"]["date"];
            $object->metaAlbum          = @$json["format"]["tags"]["album"];
            $object->metaTrackNumber    = @$json["format"]["tags"]["track"];
        }

        return $object;
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