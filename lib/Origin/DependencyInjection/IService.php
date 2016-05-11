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

/**
 * Dependency injection service interface.
 *
 * A dependency injection service represents an individual service "loader".
 * Services can be expressed as either concrete, in which case they represent
 * a specific implementation, or abstract, where they represent any implements
 * which implement a specific interface or extend a certain class.
 */
interface IService {
    /**
     * Is the instance a singleton?
     *
     * @return boolean Whether or not the instance is a singleton.
     */
    public function isSingleton();

    /**
     * Run the service loader.
     *
     * @return mixed The result from the loader callable.
     */
    public function runLoader($service_name);
}
