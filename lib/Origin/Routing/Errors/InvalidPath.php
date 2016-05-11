<?php

/**
 * Request router library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Routing\Errors;
use Exception;

class InvalidPath extends Exception {
    /**
     * Initialiser.
     *
     * @param string $path
     * @param string $method
     */
    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * @override
     */
    public function __toString() {
        return 'origin\router\error\InvalidPath: ' . $this->path;
    }
}
