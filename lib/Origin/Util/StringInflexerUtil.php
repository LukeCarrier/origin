<?php

/**
 * String inflexer utility library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util;

use Origin\Util\StringUtil;

/**
 * String inflexer utility library.
 *
 * Assorted utility functions to assist with the handling of words within the English language. The Origin framework
 * requires a degree of understanding of naming conventions in order to correctly translate class and method names
 * between formats.
 */
class StringInflexerUtil {
    /**
     * Lowercase with undescoreify a CamelCased string.
     *
     * @param string $text The text to convert.
     *
     * @return string The converted text.
     */
    public static function camelCaseToUnderscore($text) {
        return preg_replace_callback('/((?:(?<=[a-z])[A-Z])|(?:(?<=[A-Z])[A-Z](?=[a-z])))/', function ($match) {
            return '_' . strtolower($match[1]);
        }, $text);
    }

    /**
     * Pluralise a word.
     *
     * @param string $word The singular word to operate upon.
     *
     * @return string The plural form of the word.
     */
    public static function pluralise($word) {
        if (StringUtil::endsWith($word, 'y')) {
            $word = substr($word, 0, -1) . 'ie';
        } elseif (StringUtil::endsWith($word, 'ch') || StringUtil::endsWith($word, 's')) {
            $word .= 'e';
        }

        return "{$word}s";
    }

    /**
     * Given the plural variant of a word, return the plural.
     *
     * @param string $word The plural word to operate upon.
     *
     * @return string The singular form of the word.
     */
    public static function singularise($word) {
        $word = rtrim($word, 's');

        if (StringUtil::endsWith($word, 'ie')) {
            $word = substr($word, 0, -2) . 'y';
        } elseif (StringUtil::endsWith($word, 'che') || StringUtil::endsWith($word, 'se')) {
            $word = substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * CamelCaseify a lowercase string with words separated by underscores.
     *
     * @param string  $text             The text to convert.
     * @param boolean $capitalise_first If true, the first character of the string will be capitalised.
     *
     * @return string The converted text.
     */
    public static function underscoreToCamelCase($text, $capitalise_first=false) {
        $result = preg_replace_callback('/_([a-z])/', function ($match) {
            return strtoupper($match[1]);
        }, $text);

        if ($capitalise_first) {
            $result = ucfirst($result);
        }

        return $result;
    }
}
