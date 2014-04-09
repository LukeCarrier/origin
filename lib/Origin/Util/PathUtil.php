<?php

/**
 * Disk cache API.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2013 CloudFlux
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util;

/**
 * Utility methods for working with filesystem paths.
 */
class PathUtil {
    /**
     * Join together the parts of a path.
     *
     * @param string $part1 Part one of the path. You can specify a variable number of path parts ad individual
     *                      parameters to the method.
     *
     * @return string The generated path.
     */
    public static function join($part1) {
        return implode('/', func_get_args());
    }

    /**
     * Return the path to a parent directory, either immediate or arbitrary.
     *
     * @param string  $path   The path to determine the parent of.
     * @param integer $levels The number of levels to traverse upwards.
     *
     * @return string The directory's path.
     */
    public static function parent($path, $levels=1) {
        for ($i = 0; $i < $levels; $i++) {
            $path = dirname($path);
        }

        return $path;
    }
}
