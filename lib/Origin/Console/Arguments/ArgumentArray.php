<?php

/**
 * Argument parser library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2013 CloudFlux
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Console\Arguments;
use ArrayAccess,
    ArrayObject;

/**
 * An array-like object that you can retrieve parameter values from.
 *
 * @todo Move type handling logic out of this class into a separate trait; this
 *       code is pretty sought after in the config library too.
 */
class ArgumentArray extends ArrayObject implements ArrayAccess {
    /**
     * Values considered to be like boolean false.
     *
     * @var mixed[]
     */
    protected static $boolean_false = [0, 'no', 'off'];

    /**
     * Get a boolean representation of the specified key.
     *
     * @param string $key The index.
     *
     * @return boolean The type-casted value.
     */
    public function getBoolean($key) {
        return in_array($this->offsetGet($key));
    }

    /**
     * Get a float representation of the specified key.
     *
     * @param string $key The index.
     *
     * @return float The type-casted value.
     */
    public function getFloat($key) {
        return $this->get($key, static::TYPE_FLOAT);
    }

    /**
     * Get a integer representation of the specified key.
     *
     * @param string $key The index.
     *
     * @return integer The type-casted value.
     */
    public function getInteger($key) {
        return $this->get($key, static::TYPE_INTEGER);
    }

    /**
     * Get a string representation of the specified key.
     *
     * @param string $key The index.
     *
     * @return string The type-casted value.
     */
    public function getString($key) {
        return $this->get($key, static::TYPE_STRING);
    }
}
