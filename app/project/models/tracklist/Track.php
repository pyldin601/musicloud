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
use app\core\exceptions\status\PageNotFoundException;
use app\project\exceptions\BackendException;
use app\project\exceptions\TrackNotFoundException;
use app\project\libs\FFProbe;
use app\project\libs\Metadata;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\MetadataTable;
use app\project\persistence\fs\FileServer;

class Track {

    /** @var LoggedIn */
    private $me;

    private $track_id;
    private $track_data;

    private $active;

    public function __construct($track_id) {

        $this->me = resource(LoggedIn::class);

        $query = new SelectQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $track_id);

        $track = $query->fetchOneRow()->getOrThrow(TrackNotFoundException::class);

        assert($track[AudiosTable::USER_ID] == $this->me->getId(), "You have no access to this resource");

        $this->track_id = $track_id;

        $this->track_data = $track;

        $this->active = true;

    }

    public function upload($file_path, $file_name, $content_type) {

        $this->ensure();

        assert($this->track_data[AudiosTable::FILE_ID] === null, "File already uploaded");

        /** @var Metadata $metadata */
        $metadata = FFProbe::read($file_path)
            ->getOrThrow(BackendException::class, "Audio file could not be read");

        $cover = FFProbe::readTempCover($file_path);

        $file_id = FileServer::register($file_path);

        if ($cover->nonEmpty()) {
            $cover_file_id = FileServer::register($cover->get());
        } else {
            $cover_file_id = null;
        }

        (new UpdateQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $this->track_id))
            ->set(AudiosTable::FILE_ID, $file_id)
            ->set(AudiosTable::FILE_NAME, urldecode($file_name))
            ->set(AudiosTable::CONTENT_TYPE, $content_type)
            ->update();

        (new UpdateQuery(MetadataTable::TABLE_NAME, MetadataTable::ID, $this->track_id))
            ->set(MetadataTable::ALBUM, $metadata->meta_album)
            ->set(MetadataTable::ALBUM_ARTIST, $metadata->meta_album_artist)
            ->set(MetadataTable::ARTIST, $metadata->meta_artist)
            ->set(MetadataTable::DATE, $metadata->meta_date)
            ->set(MetadataTable::GENRE, $metadata->meta_genre)
            ->set(MetadataTable::TITLE, $metadata->meta_title)
            ->set(MetadataTable::TRACK_NUMBER, $metadata->meta_track_number)
            ->set(MetadataTable::BITRATE, $metadata->bitrate)
            ->set(MetadataTable::DURATION, $metadata->duration)
            ->set(MetadataTable::COVER_FILE_ID, $cover_file_id)
            ->update();

    }

    public function delete() {

        $this->ensure();

        $metadata = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->where(MetadataTable::ID, $this->track_id)
            ->fetchOneRow()
            ->get();

        if ($metadata[MetadataTable::COVER_FILE_ID] !== null) {
            FileServer::unregister($metadata[MetadataTable::COVER_FILE_ID]);
        }

        FileServer::unregister($this->track_data[AudiosTable::FILE_ID]);

        (new DeleteQuery(AudiosTable::TABLE_NAME))
            ->where(AudiosTable::ID, $this->track_id)
            ->update();

        $this->active = false;

    }

    public function preview() {

        $this->ensure();

        assert($this->track_data[AudiosTable::FILE_ID] !== null, "File not uploaded");

        header("Content-Type: " . $this->track_data[AudiosTable::CONTENT_TYPE]);

        FileServer::writeToClient($this->track_data[AudiosTable::FILE_ID]);

    }

    public function cover() {

        $this->ensure();

        assert($this->track_data[AudiosTable::FILE_ID] !== null, "File not uploaded");

        $metadata = (new SelectQuery(MetadataTable::TABLE_NAME))
            ->where(MetadataTable::ID, $this->track_id)
            ->fetchOneRow()
            ->get();

        if ($metadata[MetadataTable::COVER_FILE_ID]) {
            header("Content-Type: image/jpeg");
            FileServer::writeToClient($metadata[MetadataTable::COVER_FILE_ID]);
        } else {
            throw new PageNotFoundException;
        }


    }

    private function ensure() {
        assert($this->active, "Track deleted");
    }


} 