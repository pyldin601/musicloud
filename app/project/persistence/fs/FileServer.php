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
use app\core\etc\MIME;
use app\core\exceptions\ApplicationException;
use app\core\exceptions\status\PageNotFoundException;
use app\lang\Arrays;
use app\project\exceptions\TrackNotFoundException;
use app\project\persistence\db\tables\FilesTable;
use app\project\persistence\db\tables\MetadataTable;

class FileServer {

    const READ_BUFFER_SIZE = 4096;

    public static function class_init() {

    }

    public static function register($file_path) {

        assert(file_exists($file_path), "Audio file uploaded incorrectly");

        $hash = FSTool::calculateHash($file_path);
        $query = (new SelectQuery(FilesTable::TABLE_NAME, FilesTable::SHA1, $hash))
            ->select(FilesTable::ID);
        $file = $query->fetchOneColumn()->toInt();

        if ($file->isEmpty()) {

            FSTool::createPathUsingHash($hash);

            $id = (new InsertQuery(FilesTable::TABLE_NAME))
                ->values(FilesTable::SHA1, $hash)
                ->values(FilesTable::SIZE, filesize($file_path))
                ->values(FilesTable::USED, 1)
                ->values(FilesTable::MTIME, filemtime($file_path))
                ->values(FilesTable::CONTENT_TYPE, MIME::mime_type($file_path))
                ->executeInsert();

            rename($file_path, FSTool::filename($hash));

        } else {

            $id = $file->get();

            (new UpdateQuery(FilesTable::TABLE_NAME))
                ->increment(FilesTable::USED)
                ->where(FilesTable::ID, $id)
                ->update();

            unlink($file_path);

        }

        return $id;

    }

    public static function writeToClient($file_id) {

        $file = (new SelectQuery(FilesTable::TABLE_NAME))
            ->where(FilesTable::ID, $file_id)
            ->fetchOneRow()
            ->getOrThrow(PageNotFoundException::class);

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file[FilesTable::MTIME]) . ' GMT');
        header('Cache-Control: max-age=0');

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $file[FilesTable::MTIME]) {
            http_response_code(304);
            return;
        }

        $filename = FSTool::filename($file[FilesTable::SHA1]);
        $filesize = filesize($filename);

        $fh = fopen($filename, "rb");

        if (isset($_SERVER["HTTP_RANGE"])) {
            $range = str_replace("bytes=", "", $_SERVER["HTTP_RANGE"]);
            $start = Arrays::first(explode("-", $range));
        } else {
            $start = 0;
        }

        if (isset($range)) {
            http_response_code(206);
            header("Content-Range: bytes " . $start . "-" . ($filesize - 1) . "/" . $filesize);
            header("Content-Length: " . ($filesize - $start));
        } else {
            http_response_code(200);
            header("Content-Length: " . $filesize);
        }

        header("Accept-Ranges: bytes");

        if ($start > 0) {
            fseek($fh, $start, SEEK_SET);
        }

        set_time_limit(0);

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

        $file_data = $file->getOrThrow(ApplicationException::class, "File index corrupted!");

        if ($file_data[FilesTable::USED] > 1) {
            (new UpdateQuery(FilesTable::TABLE_NAME))
                ->decrement(FilesTable::USED)
                ->where(FilesTable::ID, $file_id)
                ->update();
        } else {

            (new DeleteQuery(FilesTable::TABLE_NAME))
                ->where(FilesTable::ID, $file[FilesTable::ID])
                ->update();

            FSTool::delete($file_data[FilesTable::SHA1]);

        }

    }

    public static function getFileUsingId($file_id) {

        $file = (new SelectQuery(FilesTable::TABLE_NAME))
            ->where(FilesTable::ID, $file_id)
            ->fetchOneRow()->getOrThrow(PageNotFoundException::class);

        return FSTool::filename($file[FilesTable::SHA1]);

    }

} 