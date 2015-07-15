<?php

namespace app\lang\option;


class Collector {

    /**
     * @return \Closure
     */
    public static function optionCombine() {
        return function (Option $a, Option $b) {
            return $a->orElse($b);
        };
    }

}
