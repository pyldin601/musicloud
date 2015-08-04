<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 04.08.2015
 * Time: 9:12
 */

namespace app\core\db\pgsimple;


interface QueryPart {
    function getQuery(\PDO $pdo);
    function getParameters();
} 