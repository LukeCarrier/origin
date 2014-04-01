<?php

/**
 * Array utility library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util;

use Origin\Util\CallableUtil;

/**
 * Supporting array functionality required for Origin.
 *
 * This class contains some extensions to the array functionality provided by PHP.
 */
class ArrayUtil {
    /**
     * Filter an array by its keys, returning key => value pairs where the key is within the array of designated keys.
     *
     * Given an array of key => value pairs and an array of permitted keys, create and return a new array containing
     * only the key => value pairs whose keys are within the array of permitted keys.
     *
     * @param mixed[] $array The array to filter.
     * @param mixed[] $keys  The permitted keys.
     *
     * @return mixed[] The filtered array.
     */
    public static function filterKeys($array, $keys) {
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = $array[$key];
        }

        return $filtered;
    }

    /**
     * Filter an array by its keys, retaining key => value pairs where the supplied callback returns true.
     *
     * Given an array and a function to pass each key through, create a new array containing only the key => value pairs
     * whose keys satisfy the passed function. Think array_filter, but for keys instead of values.
     *
     * @param mixed[]  $array    The array to filter.
     * @param callable $callback The function to call to determine whether or not to include a given key => value pair
     *                           in the resulting array. The pair will be added only if the function returns true.
     *
     * @return mixed[] The filtered array.
     */
    public static function filterKeysByCallback($array, $callback) {
        $filtered = array_filter(array_keys($array), $callback);
        return array_intersect_key($array, array_flip($filtered));
    }

    /**
     * Flatten a multi-dimensional array, preserving keys through concatenation.
     *
     * @param mixed[] $array  A multi-dimensional array.
     * @param string  $glue   The glue to use between the different levels of the array keys (defaults to '.').
     * @param string  $prefix The prefix before the name; used during recursive execution.
     *
     * @return array The singular dimensional array.
     */
    public static function flatten($array, $glue='.', $prefix='') {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result += static::flatten($value, $glue, "{$prefix}{$key}{$glue}");
            } else {
                $result["{$prefix}{$key}"] = $value;
            }
        }

        return $result;
    }

    /**
     * Implode the keys and values of an array.
     *
     * @param mixed[] $array              The array to implode. It must be possible to cast all values to strings.
     * @param string  $pair_delimiter     Separator between key => value pairs
     * @param string  $equality_delimiter Separator between keys and values.
     *
     * @return string The imploded representation of the array key => value pairs.
     */
    public static function implodeWithKeys($array, $pair_delimiter=';', $equality_delimiter='=') {
        foreach ($array as $key => &$value) {
            $value = "{$key}{$equality_delimiter}{$value}";
        }

        return implode($pair_delimiter, $array);
    }

    /**
     * Are the array's keys numerical indexes?
     *
     * @param array $array The array to check.
     *
     * @return boolean True if all keys are numeric, else false.
     */
    public static function isIndexed($array) {
        return count($array) === count(array_filter(array_keys($array), 'is_numeric'));
    }

    /**
     * array_map(), but with arguments to the callable.
     *
     * @param mixed[]  $array            The array upon which to perform the map operation.
     * @param callable $callable         The function or method to call.
     * @param mixed[]  $parameters       Additional named parameters to pass to the callable.
     * @param string   $mapped_parameter The name of the parameter to map the array value to.
     *
     * @return mixed[] The array, post-mapping.
     */
    public static function mapWithNamedParameters($array, $callable, $parameters, $mapped_parameter) {
        foreach ($array as &$value) {
            $parameters[$mapped_parameter] = $value;
            $value = CallableUtil::callWithNamedParameters($callable, $parameters);
        }

        return $array;
    }
}
