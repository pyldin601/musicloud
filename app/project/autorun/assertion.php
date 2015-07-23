<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 21.07.2015
 * Time: 15:25
 */

use app\project\exceptions\BackendException;

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);
assert_options(ASSERT_CALLBACK, function ($file, $line, $code, $desc = null) {

    if ($desc instanceof Exception) {

        throw $desc;

    } else if (is_string($desc) && class_exists($desc, false)) {

        throw new $desc;

    } else {

        throw new BackendException($desc, 400);

    }

});