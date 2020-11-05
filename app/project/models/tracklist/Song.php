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
use app\core\http\HttpStatusCodes;
use app\core\logging\Logger;
use app\libs\AudioScrobbler;
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

class Song implements \JsonSerializable {

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

        if (is_array($track_id)) {
            $this->track_data = $track_id;
        } else {
            $this->track_data = SongDao::getSongUsingId($track_id);
        }

        $this->track_id   = $this->track_data[TSongs::ID];

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
     * @param string $file_name to be used for file type detection
     */
    private function loadMetadataFromSongIntoQuery($file_path, UpdateQuery $query, $file_name = null) {

        /** @var Metadata $metadata */
        $metadata = FFProbe::read($file_path, $file_name)
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
     * @throws AlreadyUploadedException|ControllerException
     */
    public function upload($file_path, $file_name) {

        if ($this->isUploaded()) {
            throw new AlreadyUploadedException;
        }

        $query   = (new UpdateQuery(TSongs::_NAME));

        $query->where(TSongs::ID, $this->track_id);

        $this->loadCoversFromSongIntoQuery($file_path, $query);
        $this->loadMetadataFromSongIntoQuery($file_path, $query, $file_name);

        $file_id = FileServer::register($file_path);

        $query->set(TSongs::FILE_ID, $file_id)
              ->set(TSongs::FILE_NAME, $file_name)
              ->set(TSongs::C_DATE, time())
              ->set(TSongs::T_SKIPPED, 0)
              ->set(TSongs::T_PLAYED, 0);

        $query->returning("*");

        return $query->fetchAll();

    }

    public function playingCompleted() {
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

        set_time_limit(0);

        Logger::printf("Requested preview for track %s", $this->track_id);

        if ($this->hasPreview()) {
            Logger::printf("Track preview is available (file_id is %s)", $this->track_data[TSongs::PREVIEW_ID]);
            FileServer::sendToClient($this->track_data[TSongs::PREVIEW_ID]);
            return;
        }

        Logger::printf("Track preview is unavailable");
        Logger::printf("Generating new track preview in real time");

        header("Content-Type: " . PREVIEW_MIME);

        $filename  = FileServer::getFileUsingId($this->track_data[TSongs::FILE_ID]);

        $command_template = "%s -i %s -bufsize 256k -vn -ab 192k -ac 2 -acodec libmp3lame -f mp3 -";
        $command = sprintf($command_template, $this->settings->get("tools", "ffmpeg_cmd"),
            escapeshellarg($filename));

        passthru($command);

    }

    /**
     * @return mixed
     * @throws ControllerException
     */
    public function getPeaks() {

        if (!$this->hasPeaks()) {

            $new_peaks = WaveformGenerator::generate($this->getFilePath());
            $file_id = FileServer::registerByContent(json_encode($new_peaks), "application/json");

            SongDao::updateSongUsingId($this->track_id, [TSongs::PEAKS_ID => $file_id]);

        } else {

            $file_id = $this->track_data[TSongs::PEAKS_ID];

        }

        http_response_code(HttpStatusCodes::HTTP_MOVED_PERMANENTLY);
        header("Location: /file/" . $file_id);

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
        return $this->track_data[TSongs::PEAKS_ID] !== null;
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

    /**
     * @param $play_count
     * @return mixed
     */
    public function updateAndGetPlayCount($play_count) {
        if ($play_count > $this->track_data[TSongs::T_PLAYED]) {
            SongDao::updateSongUsingId($this->track_id, [
                "times_played" => $play_count
            ]);
            return $play_count;
        }
        return $this->track_data[TSongs::T_PLAYED];
    }

    /**
     * @return mixed
     */
    public function getObject() {
        return $this->track_data;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {

        $song = $this->track_data;

        $artist_encoded = escape_url($song["album_artist"]);
        $album_encoded  = escape_url($song["track_album"]);
        $genre_encoded  = escape_url($song["track_genre"]);

        $song["artist_url"] = "artist/{$artist_encoded}";
        $song["album_url"]  = "artist/{$artist_encoded}/{$album_encoded}";
        $song["genre_url"]  = "genre/{$genre_encoded}";

        return $song;

    }

}