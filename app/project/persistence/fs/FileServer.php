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
use app\core\http\HttpStatusCodes;
use app\lang\Arrays;
use app\project\exceptions\TrackNotFoundException;
use app\project\persistence\db\tables\TFiles;
use app\project\persistence\db\tables\MetadataTable;

class FileServer {

    const READ_BUFFER_SIZE = 4096;

    public static function class_init() {

    }

    public static function register($file_path) {

        assert(file_exists($file_path), "Audio file uploaded incorrectly");

        $hash = FSTool::calculateHash($file_path);
        $query = (new SelectQuery(TFiles::_NAME, TFiles::SHA1, $hash))
            ->select(TFiles::ID);
        $file = $query->fetchOneColumn();

        if ($file->isEmpty()) {

            FSTool::createPathUsingHash($hash);

            $id = (new InsertQuery(TFiles::_NAME))
                ->values(TFiles::SHA1, $hash)
                ->values(TFiles::SIZE, filesize($file_path))
                ->values(TFiles::USED, 1)
                ->values(TFiles::MTIME, filemtime($file_path))
                ->values(TFiles::CONTENT_TYPE, MIME::mime_type($file_path))
                ->returning(TFiles::ID)
                ->fetchOneColumn()
                ->get();

            rename($file_path, FSTool::filename($hash));

            error_log("Registering " . $file_path . ": NEW");

        } else {

            $id = $file->get();

            (new UpdateQuery(TFiles::_NAME))
                ->increment(TFiles::USED)
                ->where(TFiles::ID, $id)
                ->update();

            unlink($file_path);

            error_log("Registering " . $file_path . ": EXISTS");

        }

        return $id;

    }

    /**
     * @param $file
     */
    private static function write($file) {

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file[TFiles::MTIME]) . ' GMT');
        header('Cache-Control: max-age=0');
        header('Content-Type: ' . $file[TFiles::CONTENT_TYPE]);

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $file[TFiles::MTIME]) {
            http_response_code(304);
            return;
        }

        $filename = FSTool::filename($file[TFiles::SHA1]);
        $filesize = filesize($filename);

        $fh = fopen($filename, "rb");

        if (isset($_SERVER["HTTP_RANGE"])) {
            $range = str_replace("bytes=", "", $_SERVER["HTTP_RANGE"]);
            $start = Arrays::first(explode("-", $range));
        } else {
            $start = 0;
        }

        if (isset($range)) {
            http_response_code(HttpStatusCodes::HTTP_PARTIAL_CONTENT);
            header("Content-Range: bytes " . $start . "-" . ($filesize - 1) . "/" . $filesize);
            header("Content-Length: " . ($filesize - $start));
        } else {
            http_response_code(HttpStatusCodes::HTTP_OK);
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

        $file = (new SelectQuery(TFiles::_NAME))
            ->where(TFiles::ID, $file_id)
            ->fetchOneRow();

        $file_data = $file->getOrThrow(ApplicationException::class, "File already unregistered");

        if ($file_data[TFiles::USED] > 1) {
            (new UpdateQuery(TFiles::_NAME))
                ->decrement(TFiles::USED)
                ->where(TFiles::ID, $file_id)
                ->update();
        } else {

            (new DeleteQuery(TFiles::_NAME))
                ->where(TFiles::ID, $file[TFiles::ID])
                ->update();

            FSTool::delete($file_data[TFiles::SHA1]);

        }

    }

    public static function getFileUsingId($file_id) {

        $file = (new SelectQuery(TFiles::_NAME))
            ->where(TFiles::ID, $file_id)
            ->fetchOneRow()->getOrThrow(PageNotFoundException::class);

        return FSTool::filename($file[TFiles::SHA1]);

    }

    public static function sendToClient($file_id) {

        $file = (new SelectQuery(TFiles::_NAME))
            ->where(TFiles::ID, $file_id)
            ->fetchOneRow()
            ->getOrThrow(PageNotFoundException::class);

        self::write($file);

    }

} 