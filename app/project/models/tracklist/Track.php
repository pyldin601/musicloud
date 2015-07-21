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
use app\project\exceptions\TrackNotFoundException;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\tables\AudiosTable;
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

        $file_id = FileServer::register($file_path);

        (new UpdateQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $this->track_id))
            ->set(AudiosTable::FILE_ID, $file_id)
            ->set(AudiosTable::FILE_NAME, urldecode($file_name))
            ->set(AudiosTable::CONTENT_TYPE, $content_type)
            ->update();

    }

    public function delete() {

        $this->ensure();

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

    private function ensure() {
        assert($this->active, "File deleted");
    }

} 