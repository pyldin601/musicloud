<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 22.07.2015
 * Time: 14:52
 */

namespace app\core\etc;


use app\core\db\builder\SelectQuery;
use app\core\http\HttpGet;
use app\lang\option\Filter;

class Context {

    /** @var HttpGet */
    private static $request;

    /** @var Settings */
    private static $settings;

    public static function class_init() {
        self::$request = resource(HttpGet::class);
        self::$settings = resource(Settings::class);
    }

    public static function contextify(SelectQuery $query) {
        $limit = self::$request->get("l")->filter(Filter::isNumber());
        $offset = self::$request->get("o")->filter(Filter::isNumber());
        if ($offset->nonEmpty()) {
            $query->offset($offset->get());
        }
        $query->limit(min(
            $limit->getOrElse(self::$settings->get("catalog", "items_per_request_limit")),
            self::$settings->get("catalog", "items_per_request_limit")
        ));
    }
} 