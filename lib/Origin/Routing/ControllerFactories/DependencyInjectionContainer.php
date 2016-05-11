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

namespace Origin\Routing\ControllerFactories;

use Origin\DependencyInjection\Container,
    Origin\Routing\IControllerFactory;

/**
 * Dependency injection container controller factory.
 *
 * Sources instances of classes based on the services bound in an instance of
 * the Origin dependency injection container.
 */
class DependencyInjectionContainer implements IControllerFactory {
    /**
     * Dependency injection container.
     *
     * @var \Origin\DependencyInjection\Container
     */
    protected $container;

    /**
     * Initialiser.
     *
     * @param \Origin\DependencyInjection\Container $container The dependency
     *                                                         injection
     *                                                         container.
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @override \Origin\Routing\IControllerFactory
     */
    public function get($class_name) {
        return $this->container->get($class_name);
    }
}
