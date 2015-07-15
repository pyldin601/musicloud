<?php

namespace app\lang\singleton;

interface SingletonInterface {

    public static function getInstance(...$args);

    public static function hasInstance(...$args);

    public static function killInstance(...$args);

} 