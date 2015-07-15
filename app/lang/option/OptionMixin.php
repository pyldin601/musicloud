<?php

namespace app\lang\option;


trait OptionMixin {


    /*---------------------------------------------------------------*/
    /*                    Static Factory Methods                      */
    /*---------------------------------------------------------------*/

    /**
     * @param $value
     * @return Option
     */
    public static function ofNullable($value) {
        return is_null($value) ? None::instance() : new Some($value);
    }

    /**
     * @param $value
     * @param $predicate
     * @return Option
     */
    public static function of($value, $predicate) {
        return $predicate($value) ? new Some($value) : None::instance();
    }

    /**
     * @param $value
     * @return Option
     */
    public static function ofEmpty($value) {

        if (is_null($value)) return None::instance();
        if (is_array($value) && count($value) == 0) return None::instance();
        if (is_string($value) && strlen($value) == 0) return None::instance();

        return new Some($value);

    }

    /**
     * @param $value
     * @return Option
     */
    public static function ofNumber($value) {
        return is_numeric($value) ? new Some($value) : None::instance();
    }

    /**
     * @param $value
     * @return Option
     */
    public static function ofArray($value) {
        return is_array($value) ? new Some($value) : None::instance();
    }

    /**
     * @param $value
     * @return Option
     */
    public static function ofDeceptive($value) {
        return $value === false ? None::instance() : new Some($value);
    }

    /**
     * @param $filePath
     * @return Option
     */
    public static function ofFile($filePath) {
        return file_exists($filePath) ? new Some($filePath) : None::instance();
    }


}