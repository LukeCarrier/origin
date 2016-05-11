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

class NoMatchingRoute extends Exception {
    /**
     * Initialiser.
     *
     * @param string $path
     * @param string $method
     */
    public function __construct($path, $method) {
        $this->path   = $path;
        $this->method = $method;
    }

    /**
     * @override
     */
    public function __toString() {
        $route = "{$this->method} {$this->path}";
        return 'origin\router\error\NoMatchingRouteError: ' . $route;
    }
}
