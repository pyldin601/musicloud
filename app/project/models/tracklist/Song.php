<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:56
 */

namespace app\project\models\tracklist;


use app\core\cache\TempFileProvider;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\etc\Settings;
use app\core\exceptions\ControllerException;
use app\core\exceptions\status\PageNotFoundException;
use app\core\logging\Logger;
use app\libs\WaveformGenerator;
use app\project\exceptions\AlreadyUploadedException;
use app\project\exceptions\BadAccessException;
use app\project\exceptions\InvalidAudioFileException;
use app\project\libs\FFProbe;
use app\project\libs\Metadata;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;

class Song {

    /** @var LoggedIn */
    private $me;

    private $track_id;
    private $track_data;

    /** @var Settings */
    private $settings;

    /**
     * @param $track_id
     * @throws BadAccessException
     */
    public function __construct($track_id) {

        $this->settings = resource(Settings::class);
        $this->me       = resource(LoggedIn::class);

        $this->track_data = SongDao::getSongUsingId($track_id);
        $this->track_id   = $track_id;

        $this->checkPermission();

    }

    /**
     * @throws BadAccessException
     */
    private function checkPermission() {
        if ($this->track_data[TSongs::USER_ID] !== $this->me->getId()) {
            throw new BadAccessException;
        };
    }

    /**
     * Reads covers from $file_path into FileServer and
     * passes data to $query.
     *
     * @param $file_path
     * @param UpdateQuery $query
     */
    private function loadCoversFromSongIntoQuery($file_path, UpdateQuery $query) {

        $covers = FFProbe::readTempCovers($file_path);

        if ($covers->nonEmpty()) {

            $full_cover_id   = FileServer::register($covers->get()[0]);
            $middle_cover_id = FileServer::register($covers->get()[1]);
            $small_cover_id  = FileServer::register($covers->get()[2]);

            $query->set(TSongs::C_SMALL_ID, $small_cover_id)
                  ->set(TSongs::C_MID_ID, $middle_cover_id)
                  ->set(TSongs::C_BIG_ID, $full_cover_id);

        }

    }

    /**
     * Passes metadata from $file_path into $query builder.
     * If $file_path is invalid audio file throws exception.
     *
     * @param $file_path
     * @param UpdateQuery $query
     * @throws InvalidAudioFileException
     */
    private function loadMetadataFromSongIntoQuery($file_path, UpdateQuery $query) {

        /** @var Metadata $metadata */
        $metadata = FFProbe::read($file_path)
            ->getOrThrow(InvalidAudioFileException::class);

        $query->set(TSongs::T_ARTIST,  $metadata->meta_artist)
              ->set(TSongs::T_YEAR,    $metadata->meta_date)
              ->set(TSongs::T_TITLE,   $metadata->meta_title)
              ->set(TSongs::T_NUMBER,  $metadata->meta_track_number)
              ->set(TSongs::DISC,      $metadata->meta_disc_number)
              ->set(TSongs::BITRATE,   $metadata->bitrate)
              ->set(TSongs::LENGTH,    $metadata->duration)
              ->set(TSongs::A_ARTIST,  $metadata->meta_album_artist)
              ->set(TSongs::T_GENRE,   $metadata->meta_genre)
              ->set(TSongs::T_ALBUM,   $metadata->meta_album)
              ->set(TSongs::T_COMMENT, $metadata->meta_comment)
              ->set(TSongs::IS_COMP,   $metadata->is_compilation)
              ->set(TSongs::FORMAT,    $metadata->format_name);

    }

    /**
     * @param $file_path
     * @param $file_name
     * @return array Created Song record
     * @throws AlreadyUploadedException
     */
    public function upload($file_path, $file_name) {

        if ($this->isUploaded()) {
            throw new AlreadyUploadedException;
        }

        $file_id = FileServer::register($file_path);
        $query   = (new UpdateQuery(TSongs::_NAME));

        $query->where(TSongs::ID, $this->track_id);

        $this->loadCoversFromSongIntoQuery($file_path, $query);
        $this->loadMetadataFromSongIntoQuery($file_path, $query);

        $query->set(TSongs::FILE_ID, $file_id)
              ->set(TSongs::FILE_NAME, $file_name)
              ->set(TSongs::C_DATE, time());

        $query->returning("*");

        return $query->fetchAll();

    }

    public function incrementPlays() {
        SongDao::incrementUsingId($this->track_id, TSongs::T_PLAYED);
    }

    public function incrementSkips() {
        SongDao::incrementUsingId($this->track_id, TSongs::T_SKIPPED);
    }

    public function setRating($rating) {
        SongDao::updateSongUsingId($this->track_id, [
            TSongs::T_RATING => $rating
        ]);
    }

    public function removeRating() {
        SongDao::updateSongUsingId($this->track_id, [
            TSongs::T_RATING => null
        ]);
    }

    /**
     * Reads audio file record from database and generates audio preview.
     */
    public function preview() {

        Logger::printf("Requested preview for track %s", $this->track_id);

        if ($this->hasPreview()) {
            Logger::printf("Track preview is available (file_id is %s)", $this->track_data[TSongs::PREVIEW_ID]);
            FileServer::sendToClient($this->track_data[TSongs::PREVIEW_ID]);
            return;
        }

        Logger::printf("Track preview is unavailable");
        Logger::printf("Generating new track preview in real time");

        header("Content-Type: " . PREVIEW_MIME);

        $temp_file = TempFileProvider::generate("preview", ".mp3");
        $filename  = FileServer::getFileUsingId($this->track_data[TSongs::FILE_ID]);

        $command_template = "%s -i %s -bufsize 256k -vn -ab 128k -ac 2 -acodec libmp3lame -f mp3 - | tee %s";
        $command = sprintf($command_template, $this->settings->get("tools", "ffmpeg_cmd"),
            escapeshellarg($filename), escapeshellarg($temp_file));

        passthru($command);

        $temp_file_id = FileServer::register($temp_file, PREVIEW_MIME);

        Logger::printf("New preview registered under file_id %s", $temp_file_id);

        SongDao::updateSongUsingId($this->track_id, [
            TSongs::PREVIEW_ID => $temp_file_id
        ]);

    }

    /**
     * @return mixed
     * @throws ControllerException
     */
    public function getPeaks() {

        if (!$this->hasPeaks()) {

            $new_peaks = WaveformGenerator::generate($this->getFilePath());

            SongDao::updateSongUsingId($this->track_id, [
                "peaks" => "{" . implode(",", $new_peaks) . "}"
            ]);

            return json_encode($new_peaks);

        } else {

            return (new SelectQuery(TSongs::_NAME))
                ->where(TSongs::ID, $this->track_id)
                ->select("ARRAY_TO_JSON(" . TSongs::PEAKS . ")")
                ->fetchColumn()
                ->get();

        }

    }

    /**
     * @return bool
     */
    private function hasPreview() {
        return $this->track_data[TSongs::PREVIEW_ID] !== null;
    }

    /**
     * @return bool
     */
    private function hasPeaks() {
        return $this->track_data[TSongs::PEAKS] !== null;
    }

    /**
     * @return bool
     */
    private function isUploaded() {
        return $this->track_data[TSongs::FILE_ID] !== null;
    }

    /**
     * @return mixed
     */
    private function getFilePath() {
        return FileServer::getFileUsingId($this->track_data[TSongs::FILE_ID]);
    }

}