<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:40
 */

namespace app\core\db\builder;


interface QueryBuilder {
    public function getQuery(\PDO $pdo);
    public function getParameters();
} 