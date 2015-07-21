<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:54
 */

namespace app\project\persistence\fs;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\project\exceptions\TrackNotFoundException;
use app\project\persistence\db\tables\FilesTable;

class FileServer {

    const READ_BUFFER_SIZE = 4096;

    public static function class_init() {

    }

    public static function register($file_path) {

        assert(file_exists($file_path), "Track file uploaded incorrectly");

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

        return $id;

    }

    public static function writeToClient($file_id) {

        $file = (new SelectQuery(FilesTable::TABLE_NAME))
            ->where(FilesTable::ID, $file_id)
            ->fetchOneRow()
            ->getOrThrow(TrackNotFoundException::class);

        $filename = FSTool::filename($file[FilesTable::SHA1]);

        $fh = fopen($filename, "r");

        if (isset($_SERVER["HTTP_RANGE"])) {
            $range = $_SERVER["HTTP_RANGE"];
            $range = str_replace("bytes=", "", $range);
            list($start, $end) = explode("-", $range);
        } else {
            $start = 0;
        }

        if (isset($range)) {
            http_response_code(206);
        } else {
            http_response_code(200);
        }

        header("Accept-Ranges: bytes");
        header("Access-Control-Allow-Headers: range, accept-encoding");
        header("Content-Length: " . $file[FilesTable::SIZE]);

        if (isset($range)) {
            header("Content-Range: bytes " . $start . "-" . ($file[FilesTable::SIZE] - 1) . "/" . $file[FilesTable::SIZE]);
        }

        if ($start > 0) {
            fseek($fh, $start, SEEK_SET);
        }

        set_time_limit(600);

        while ($data = fread($fh, self::READ_BUFFER_SIZE)) {
            echo $data;
            flush();
        }

        fclose($fh);

    }

    public static function unregister($file_id) {

        $file = (new SelectQuery(FilesTable::TABLE_NAME))
            ->where(FilesTable::ID, $file_id)
            ->fetchOneRow();

        $file_data = $file->get();

        if ($file_data[FilesTable::USED] > 1) {
            (new UpdateQuery(FilesTable::TABLE_NAME))
                ->decrement(FilesTable::USED)
                ->where(FilesTable::ID, $file_id)
                ->update();
        } else {

            (new DeleteQuery(FilesTable::TABLE_NAME))
                ->where(FilesTable::ID, $file_id)
                ->update();

            FSTool::delete($file_data[FilesTable::SHA1]);

        }

    }

} 