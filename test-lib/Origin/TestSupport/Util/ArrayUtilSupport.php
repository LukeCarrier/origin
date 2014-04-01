<?php

namespace Origin\TestSupport\Util;

class ArrayUtilSupport {
    public static function divide($number, $divisor) {
        return $number / $divisor;
    }

    public static function notCalledSteve($name) {
        list($forename, $surname) = explode(' ', $name, 2);
        return $forename !== 'Steve';
    }
}
