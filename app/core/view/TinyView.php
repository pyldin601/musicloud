<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 02.07.15
 * Time: 22:01
 */

namespace app\core\view;


class TinyView {

    const TEMPLATES_PATH = "app/project/templates/";

    public static function show($template, $context = null) {
        if ($context) {
            extract($context);
        }
        eval("?>" . file_get_contents(self::TEMPLATES_PATH . $template));
    }

}