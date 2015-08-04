<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:56
 */

namespace app\project\models\tracklist;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\etc\MIME;
use app\core\etc\Settings;
use app\core\exceptions\ApplicationException;
use app\core\exceptions\status\PageNotFoundException;
use app\libs\WaveformGenerator;
use app\project\exceptions\AlreadyUploadedException;
use app\project\exceptions\BackendException;
use app\project\exceptions\BadAccessException;
use app\project\exceptions\InvalidAudioFileException;
use app\project\exceptions\TrackNotFoundException;
use app\project\libs\FFProbe;
use app\project\libs\Metadata;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\dao\AlbumDao;
use app\project\persistence\db\dao\ArtistDao;
use app\project\persistence\db\dao\GenreDao;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\CoversTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\db\tables\StatsTable;
use app\project\persistence\db\tables\TFiles;
use app\project\persistence\db\tables\TSongs;
use app\project\persistence\fs\FileServer;
use app\project\persistence\fs\FSTools;

class Track {

    /** @var LoggedIn */
    private $me;

    private $track_id;
    private $track_data;

    /** @var Settings */
    private $settings;

    public function __construct($track_id) {

        $this->settings     = resource(Settings::class);
        $this->me           = resource(LoggedIn::class);

        $track = (new SelectQuery(TSongs::_NAME, TSongs::ID, $track_id))
            ->fetchOneRow()
            ->getOrThrow(TrackNotFoundException::class);

        assert($track[TSongs::USER_ID] == $this->me->getId(), BadAccessException::class);

        $this->track_id = $track_id;
        $this->track_data = $track;

    }

    public function upload($file_path, $file_name) {

        assert($this->track_data[TSongs::FILE_ID] === null, AlreadyUploadedException::class);

        /** @var Metadata $metadata */
        $metadata = FFProbe::read($file_path)
            ->getOrThrow(InvalidAudioFileException::class);

        $covers  = FFProbe::readTempCover($file_path);
        $file_id = FileServer::register($file_path);

        $query = (new UpdateQuery(TSongs::_NAME));

        if ($covers->nonEmpty()) {

            $full_cover_id      = FileServer::register($covers->get()[0]);
            $middle_cover_id    = FileServer::register($covers->get()[1]);
            $small_cover_id     = FileServer::register($covers->get()[2]);

            $query  ->set(TSongs::C_SMALL_ID, $small_cover_id)
                    ->set(TSongs::C_MID_ID, $middle_cover_id)
                    ->set(TSongs::C_BIG_ID, $full_cover_id);

        }

        $query  ->set(TSongs::FILE_ID,   $file_id)
                ->set(TSongs::FILE_NAME, $file_name);

        $query  ->set(TSongs::T_ARTIST,  $metadata->meta_artist)
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
                ->set(TSongs::C_DATE,    time());

        $query->where(TSongs::ID, $this->track_id);
        $query->returning("*");

        return $query->fetchAll();

    }

//    public function getPeaks() {
//        return WaveformGenerator::generate(FileServer::getFileUsingId($this->track_data[AudiosTable::FILE_ID]));
//    }

    public function incrementPlays() {
        (new UpdateQuery(TSongs::_NAME, TSongs::ID, $this->track_id))
            ->increment(TSongs::T_PLAYED)
            ->set(TSongs::LP_DATE, time())
            ->update();
    }

    public function incrementSkips() {
        (new UpdateQuery(TSongs::_NAME, TSongs::ID, $this->track_id))
            ->increment(TSongs::T_SKIPPED)
            ->update();
    }

    public function setRating($rating) {
        (new UpdateQuery(TSongs::_NAME, TSongs::ID, $this->track_id))
            ->set(TSongs::T_RATING, $rating)
            ->update();
    }

    public function removeRating() {
        (new UpdateQuery(TSongs::_NAME, TSongs::ID, $this->track_id))
            ->set(TSongs::T_RATING, null)
            ->update();
    }

    /**
     * Reads audio file record from database and generates audio preview.
     */
    public function preview() {

        $filename = (new SelectQuery(TFiles::_NAME))
            ->where(TFiles::ID, $this->track_data[TSongs::FILE_ID])
            ->select(TFiles::SHA1)
            ->fetchColumn()
            ->map([FSTools::class, "hashToFullPath"])
            ->map("escapeshellarg")
            ->getOrThrow(ApplicationException::class, "File associated with audio track not found");

        $command_template = "%s -loglevel quiet -i %s -ab 96k -ac 2 -acodec libfdk_aac -profile:a aac_he_v2 -f adts -";
        $command = sprintf($command_template, $this->settings->get("tools", "ffmpeg_cmd"), $filename);

        header("Content-Type: audio/aac");

        passthru($command);

    }

} 