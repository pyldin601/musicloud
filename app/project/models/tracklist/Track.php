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
use app\project\persistence\fs\FileServer;
use app\project\persistence\fs\FSTool;

class Track {

    /** @var LoggedIn */
    private $me;

    private $track_id;
    private $track_data;

    private $settings;

    public function __construct($track_id) {

        $this->me = resource(LoggedIn::class);

        $query = new SelectQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $track_id);

        $track = $query->fetchOneRow()->getOrThrow(TrackNotFoundException::class);

        assert($track[AudiosTable::USER_ID] == $this->me->getId(), BadAccessException::class);

        $this->track_id = $track_id;

        $this->track_data = $track;

        $this->settings = Settings::getInstance();

    }

    public function upload($file_path, $file_name) {

        $mime_type = MIME::mime_type($file_path);

        assert($this->track_data[AudiosTable::FILE_ID] === null, AlreadyUploadedException::class);

        /** @var Metadata $metadata */
        $metadata = FFProbe::read($file_path)
            ->getOrThrow(InvalidAudioFileException::class);


        $covers = FFProbe::readTempCover($file_path);

        $file_id = FileServer::register($file_path);

        if ($covers->nonEmpty()) {

            $full_cover_id = FileServer::register($covers->get()[0]);
            $middle_cover_id = FileServer::register($covers->get()[1]);
            $small_cover_id = FileServer::register($covers->get()[2]);

            (new UpdateQuery(CoversTable::TABLE_NAME, CoversTable::ID_FULL, $this->track_id))
                ->set(CoversTable::COVER_FULL_FULL, $full_cover_id)
                ->set(CoversTable::COVER_MIDDLE_FULL, $middle_cover_id)
                ->set(CoversTable::COVER_SMALL_FULL, $small_cover_id)
                ->update();

        }

        (new UpdateQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $this->track_id))
            ->set(AudiosTable::FILE_ID,             $file_id)
            ->set(AudiosTable::FILE_NAME,           $file_name)
            ->set(AudiosTable::CONTENT_TYPE,        $mime_type)
            ->update();

        $artist_id = ArtistDao::getArtistId($metadata->meta_album_artist ?: "");
        $genre_id  = GenreDao::getGenreId($metadata->meta_genre ?: "");
        $album_id  = AlbumDao::getAlbumId($artist_id, $metadata->meta_album ?: "");

        (new UpdateQuery(MetadataTable::TABLE_NAME, MetadataTable::ID, $this->track_id))
            ->set(MetadataTable::ARTIST,            $metadata->meta_artist)
            ->set(MetadataTable::DATE,              $metadata->meta_date)
            ->set(MetadataTable::TITLE,             $metadata->meta_title)
            ->set(MetadataTable::TRACK_NUMBER,      $metadata->meta_track_number)
            ->set(MetadataTable::BITRATE,           $metadata->bitrate)
            ->set(MetadataTable::DURATION,          $metadata->duration)
            ->set(MetadataTable::ARTIST_ID,         $artist_id)
            ->set(MetadataTable::GENRE_ID,          $genre_id)
            ->set(MetadataTable::ALBUM_ID,          $album_id)
            ->update();

    }

    public function getPeaks() {
        return WaveformGenerator::generate(FileServer::getFileUsingId($this->track_data[AudiosTable::FILE_ID]));
    }

    public function incrementPlays() {
        (new UpdateQuery(StatsTable::TABLE_NAME, StatsTable::ID_FULL, $this->track_id))
            ->increment(StatsTable::PLAYBACKS_FULL)
            ->set(StatsTable::LAST_PLAYED_DATE_FULL, time())
            ->update();
    }

    public function incrementSkips() {
        (new UpdateQuery(StatsTable::TABLE_NAME, StatsTable::ID_FULL, $this->track_id))
            ->increment(StatsTable::SKIPS_FULL)
            ->update();
    }


} 