<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:54
 */

namespace app\project\persistence\fs;


use app\core\cache\TempFileProvider;
use app\core\db\builder\DeleteQuery;
use app\core\db\builder\InsertQuery;
use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\core\etc\MIME;
use app\core\exceptions\ApplicationException;
use app\core\exceptions\ControllerException;
use app\core\exceptions\status\PageNotFoundException;
use app\core\http\HttpStatusCodes;
use app\core\logging\Logger;
use app\lang\Arrays;
use app\lang\MLArray;
use app\lang\option\Option;
use app\project\persistence\db\tables\TFiles;
use app\project\persistence\db\tables\TSongs;

class FileServer {

    const READ_BUFFER_SIZE = 4096;

    public static function class_init() {

    }

    public static function registerByContent($file_content, $content_type) {
        $temp_file = TempFileProvider::generate();
        file_put_contents($temp_file, $file_content);
        return self::register($temp_file, $content_type);
    }

    public static function register($file_path, $content_type = null) {

        if (!file_exists($file_path)) {
            throw new ControllerException(sprintf("File \"%s\" not exists!", $file_path));
        }

        $size = filesize($file_path);

        Logger::printf("Registering file %s (%d) on file server", $file_path, $size);

        $hash = FSTools::calculateHash($file_path);
        $query = (new SelectQuery(TFiles::_NAME, TFiles::SHA1, $hash))
            ->select(TFiles::ID);
        $file = $query->fetchColumn();

        if ($file->isEmpty()) {

            FSTools::createPathUsingHash($hash);

            $id = (new InsertQuery(TFiles::_NAME))
                ->values(TFiles::SHA1, $hash)
                ->values(TFiles::SIZE, $size)
                ->values(TFiles::USED, 1)
                ->values(TFiles::MTIME, filemtime($file_path))
                ->values(TFiles::CONTENT_TYPE, $content_type ?: MIME::mime_type($file_path))
                ->returning(TFiles::ID)
                ->fetchColumn()
                ->get();

            rename($file_path, FSTools::hashToFullPath($hash));

        } else {

            $id = $file->get();

            (new UpdateQuery(TFiles::_NAME))
                ->increment(TFiles::USED)
                ->where(TFiles::ID, $id)
                ->update();

            unlink($file_path);

        }

        return $id;

    }

    /**
     * @param $file
     * @throws ApplicationException
     */
    private static function write($file) {

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file[TFiles::MTIME]) . ' GMT');
        header('Cache-Control: max-age=0');
        header('Content-Type: ' . $file[TFiles::CONTENT_TYPE]);

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $file[TFiles::MTIME]) {
            http_response_code(304);
            return;
        }

        $filename = FSTools::hashToFullPath($file[TFiles::SHA1]);

        if (! file_exists($filename)) {
            throw new ApplicationException("Internal server error: file not exists!");
        }

        $filesize = filesize($filename);


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

        set_time_limit(0);

        withOpenedFile($filename, "rb", function ($fh) use ($start) {

            if ($start > 0) fseek($fh, $start, SEEK_SET);

            while ($data = fread($fh, self::READ_BUFFER_SIZE)) {
                echo $data;
                flush();
            }

        });

    }

    public static function unregister($file_id) {


        $file = (new SelectQuery(TFiles::_NAME))
            ->where(TFiles::ID, $file_id)
            ->fetchOneRow();


        $file_data = $file->getOrThrow(ApplicationException::class, "File already unregistered");

        Logger::printf("Un-registering file %s (%s) on file server", $file_data[TFiles::ID], $file_data[TFiles::CONTENT_TYPE]);

        if ($file_data[TFiles::USED] > 1) {

            Logger::printf("Decreasing usage count to %d", $file_data[TFiles::USED] - 1);

            (new UpdateQuery(TFiles::_NAME))
                ->decrement(TFiles::USED)
                ->where(TFiles::ID, $file_id)
                ->update();

        } else {

            Logger::printf("Removing file completely");

            (new DeleteQuery(TFiles::_NAME))
                ->where(TFiles::ID, $file_id)
                ->update();

            FSTools::delete($file_data[TFiles::SHA1]);

        }

    }

    public static function getFileUsingId($file_id) {

        return self::findFileUsingId($file_id)->getOrThrow(PageNotFoundException::class);

    }

    /**
     * @param $file_id
     * @return Option
     */
    public static function findFileUsingId($file_id) {

        return (new SelectQuery(TFiles::_NAME))
            ->select(TFiles::SHA1)
            ->where(TFiles::ID, $file_id)
            ->fetchColumn()
            ->map(array(FSTools::class, "hashToFullPath"));

    }

    public static function sendToClient($file_id) {

        $file = (new SelectQuery(TFiles::_NAME))
            ->where(TFiles::ID, $file_id)
            ->fetchOneRow()
            ->getOrThrow(PageNotFoundException::class);

        self::write($file);

    }

    public static function removeDead() {

        $accumulator = [];

        Logger::printf("Checking file server records...");
        (new SelectQuery(TFiles::_NAME))
            ->select(TSongs::ID)
            ->eachRow(function ($row) use (&$accumulator) {
                $path = self::getFileUsingId($row[TSongs::ID]);
                if (! file_exists($path)) {
                    Logger::printf("File id %s not exists", $row[TSongs::ID]);
                    $accumulator[] = $row[TSongs::ID];
                }
            });


        if (count($accumulator)) {
            Logger::printf("%d file record(s) is waiting to be removed", count($accumulator));
            (new DeleteQuery(TFiles::_NAME))
                ->where(TFiles::ID, $accumulator)
                ->update();
        }


    }

} 