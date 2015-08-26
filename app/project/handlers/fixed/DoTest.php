<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 01.08.15
 * Time: 00:14
 */

namespace app\project\handlers\fixed;


use app\core\db\DatabaseConnection;
use app\core\router\RouteHandler;

class DoTest implements RouteHandler {
    public function doGet() {
        header("Content-Type: text/plain");
        DatabaseConnection::doInConnection(function (DatabaseConnection $db) {
            $generator = $db->getGenerator("SELECT * FROM songs");
            $temp1 = $generator->map(function ($row) {
                return $row['file_name'];
            });
            $temp2 = $temp1->filter(function ($row) {
                return substr($row, 0, 1) == "A";
            });
            foreach ($temp2 as $row) {
                echo $row . PHP_EOL;
            }
        });
    }
}

