<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 16.07.2015
 * Time: 10:38
 */

namespace app\core\db\builder;


use app\lang\Tools;

class DeleteQuery extends BaseQuery {

    use WhereSection;

    function __construct($tableName, $key = null, $value = null) {
        $this->tableName = $tableName;
        if (!Tools::isNull($key, $value)) {
            $this->where($key, $value);
        }
    }


    public function getQuery(\PDO $pdo) {

        $build = [];

        $build[] = "DELETE FROM";
        $build[] = $this->tableName;
        $build[] = $this->buildWheres($pdo);
        $query[] = $this->buildOrderBy();
        $query[] = $this->buildLimits();

        return implode(" ", $build);

    }

} 