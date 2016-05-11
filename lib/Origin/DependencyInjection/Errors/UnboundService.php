<?php

/**
 * Dependency injection library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\DependencyInjection\Errors;
use Exception;

/**
 * Unbound service exception.
 *
 * Raised on attempts to get instances of services that have not yet been bound
 * within the controller.
 */
class UnboundService extends Exception {
    /**
     * The name of the unbound service requested.
     *
     * @var string
     */
    protected $service_name;

    /**
     * @override
     *
     * @param string $service_name The name of the unbound service requested.
     */
    public function __construct($service_name) {
        $this->service_name = $service_name;
    }

    /**
     * @override
     */
    public function __toString() {
        return 'origin\dependencyinjection\errors\UnboundService: '
                . $this->service_name;
    }

    /**
     * Get the name of the unbound service requested.
     *
     * @return string
     */
    public function getServiceName() {
        return $this->service_name;
    }
}
