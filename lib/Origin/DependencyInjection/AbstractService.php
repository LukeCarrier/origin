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

namespace Origin\DependencyInjection;

use Origin\Util\CallableUtil;

/**
 * Dependency injection service.
 *
 * A dependency injection service represents an individual loader and singleton
 * flag.
 */
class AbstractService implements IService {
    /**
     * Loader callable.
     *
     * @var callable
     */
    protected $loader;

    /**
     * Is the instance a singleton?
     *
     * @var boolean
     */
    protected $singleton;

    /**
     * Initialiser.
     *
     * @param callable $loader     The callable through which to instantiate the service.
     * @param boolean  $singleton  Whether or not the instance is a singleton.
     */
    public function __construct($loader, $singleton) {
        $this->loader     = $loader;
        $this->singleton  = $singleton;
    }

    /**
     * @override \Origin\DependencyInjection\IService
     */
    public function isSingleton() {
        return $this->singleton;
    }

    /**
     * @override \Origin\DependencyInjection\IService
     */
    public function runLoader($service_name) {
        $loader     = $this->loader;
        $parameters = $loader();

        return CallableUtil::instantiateWithNamedParameters($service_name, $parameters);
    }
}
