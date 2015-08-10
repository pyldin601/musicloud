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
use app\lang\option\Mapper;

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

        $limit  = self::$request->get("l")
            ->filter(Filter::isNumber())
            ->filter(Filter::isPositiveNumber());

        $offset = self::$request->get("o")
            ->filter(Filter::isNumber())
            ->filter(Filter::isPositiveNumber());

        $sort_field = self::$request->get("sf");
        $sort_order = self::$request->get("so");

        $max_limit = self::$settings->get("catalog", "items_per_request_limit");

        $query->limit($limit->filter(Filter::isLessThan($max_limit))->getOrElse($max_limit));
        $query->offset($offset->orZero());

    }
} 