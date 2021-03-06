<?php

/**
 * String utilities library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util;

/**
 * Extended string functions.
 */
class StringUtil {
    /**
     * Does the specified string contain the specified needle?
     *
     * A prettier way of using substr_count().
     *
     * @param string $haystack The string to search within.
     * @param string $needle   The substring we're seeking.
     *
     * @return boolean True if the needle exists within the haystack, else false.
     */
    public static function contains($haystack, $needle) {
        return !!substr_count($haystack, $needle);
    }

    /**
     * Does the specified string end with the specified needle?
     *
     * @param string $haystack The string to search within.
     * @param string $needle   The substring we're seeking.
     *
     * @return boolean True if the needle exists at the end of the string, else false.
     */
    public static function endsWith($haystack, $needle) {
        return (substr($haystack, strlen($haystack) - strlen($needle)) === $needle);
    }

    /**
     * Substitute parameters into the supplied format string.
     *
     * Iterates over the key => value pairs in the supplied parameter array, seeking curly brace-enclosed occurences of
     * each key, replacing them with the corresponding value.
     *
     * @param string   $string     The format string.
     * @param string[] $parameters A string-indexed array of parameters to substitute into the format string.
     *
     * @return string The formatted string.
     */
    public static function format($string, $parameters) {
        $search = array_map(function ($key) {
            return '{' . $key . '}';
        }, array_keys($parameters));

        return str_replace($search, array_values($parameters), $string);
    }

    /**
     * Does the specified string start with the specified needle?
     *
     * @param string $haystack The string to search within.
     * @param string $needle   The substring we're seeking.
     *
     * @return boolean True if the needle exists at the start of the string, else false.
     */
    public static function startsWith($haystack, $needle) {
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }

    /**
     * Does the specified string start and end with the specified needles?
     *
     * @param string $haystack   The string to search within.
     * @param string $needlePre  The substring we're seeking for at the start of the string.
     * @param string $needlePost The substring we're seeking for at the end of the string.
     *
     * @return boolean True if the needles exist at the start and end of the string, else false.
     */
    public static function surroundedBy($haystack, $needlePre, $needlePost) {
        if (static::startsWith($haystack, $needlePre) && static::endswith($haystack, $needlePost)) {
            return trim(rtrim($haystack, $needlePost), $needlePre);
        } else {
            return false;
        }
    }
}
