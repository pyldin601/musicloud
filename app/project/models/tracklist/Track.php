<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 14:56
 */

namespace app\project\models\tracklist;


use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\project\exceptions\BadAccessException;
use app\project\exceptions\TrackNotFoundException;
use app\project\models\single\LoggedIn;
use app\project\models\Tracks;
use app\project\persistence\db\tables\AudiosTable;
use app\project\persistence\db\tables\FilesTable;
use app\project\persistence\fs\FSTool;

class Track {

    /** @var LoggedIn */
    private $me;

    private $track_id;

    private $track_data;

    public function __construct($track_id) {

        $this->me = resource(LoggedIn::class);

        $query = new SelectQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $track_id);
        $track = $query->fetchOneRow()->getOrElse(TrackNotFoundException::class);

        assert($track[AudiosTable::USER_ID] == $this->me->getId(), "You have no access to this resource");

        $this->track_id = $track_id;

        $this->track_data = $track;

    }

    public function load($file_path, $file_name) {

        $hash = FSTool::calculateHash($file_path);
        $query = new SelectQuery(FilesTable::TABLE_NAME, FilesTable::SHA1, $hash);
        $file = $query->fetchOneRow();

        if ($file->isEmpty()) {

            FSTool::createPathUsingHash($hash);

            $id = (new InsertQuery(FilesTable::TABLE_NAME))
                ->values(FilesTable::SHA1, $hash)
                ->values(FilesTable::SIZE, filesize($file_path))
                ->values(FilesTable::USED, 1)
                ->executeInsert();

            move_uploaded_file($file_path, FSTool::filename($hash));

        } else {

            $id = $file->get()[FilesTable::ID];

            (new UpdateQuery(FilesTable::TABLE_NAME))
                ->increment(FilesTable::USED)
                ->where(FilesTable::ID, $id)
                ->update();


        }

        (new UpdateQuery(AudiosTable::TABLE_NAME, AudiosTable::ID, $this->track_id))
            ->set(AudiosTable::FILE_ID, $id)
            ->set(AudiosTable::FILE_NAME, $file_name)
            ->update();

    }

} 