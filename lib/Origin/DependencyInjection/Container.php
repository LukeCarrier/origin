<?php

/**
 * Dependency injection library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\DependencyInjection;

use ArrayAccess,
    Origin\DependencyInjection\Errors\UnboundService as UnboundServiceError,
    ReflectionClass;

/**
 * Dependency injection container.
 *
 * The dependency injection container provides a flexible and approachable approach to managing complex dependency trees
 * within your application. By binding loaders to initialise your application's services, you can decouple components of
 * your application from one another.
 *
 * To use the container, you must first bind your service loaders to it. It's recommended that you follow some form of
 * convention when choosing names for bound services, so as to ease with debugging in the future. You can bind
 * instances, classes and initialisation functions.
 */
class Container implements ArrayAccess {
    /**
     * Live instances.
     *
     * Contains singleton instances which are have been initialised and can be returned without further computation.
     *
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * Bound services.
     *
     * Array of bound services indexed by service name.
     *
     * @var \Origin\DependencyInjection\IService[]
     */
    protected $services = [];

    /**
     * Initialiser.
     *
     * Sets up the container's default property values.
     */
    public function __construct() {
        $this->instances = [];
        $this->services  = [];
    }

    /**
     * Bind an abstract class.
     */
    public function bindAbstractClass($class_name, $singleton=true) {
        $this->bindLoader($class_name, function() use($class_name) {
            return new $class_name();
        });

        return $this;
    }

    public function bindAbstractLoader($class_name, $service_loader, $singleton=true) {
        $this->services[$class_name] = new AbstractService(
            $service_loader->bindTo($this),
            $singleton
        );
    }

    /**
     * Bind a class.
     *
     * Binds a class that has no dependencies/parameters.
     *
     * @param string  $service_name The name of the service to bind.
     * @param string  $class_name   The name of the class which should be instantiated upon attempts to retrieve the
     *                              service. If null, it will be assumed to be the same as the service name.
     * @param boolean $singleton    Whether or not the container should retain the instance of the class intantiated on
     *                              the first retrieval attempt, thus "sharing" a singleton instance.
     *
     * @return void
     */
    public function bindClass($service_name, $class_name=null, $singleton=true) {
        $class_name = $class_name ?: $service_name;

        $this->bindLoader($service_name, function() use($class_name) {
            return new $class_name();
        }, $singleton);

        return $this;
    }

    /**
     * Bind an existing instance.
     *
     * Forego instantiating the class entirely and just bind an existing
     * instance. This method is ideal for binding any objects required for early
     * initialisation, like symbol autoloaders.
     *
     * Note: bound instances are always considered singletons.
     *
     * @param string $service_name The name of the service to bind.
     * @param mixed  $instance     The instance to bind.
     */
    public function bindInstance($service_name, $instance) {
        $this->services[$service_name]  = new ConcreteService(NULL, true);
        $this->instances[$service_name] = $instance;

        return $this;
    }

    /**
     * Bind a service loader.
     *
     * A service loader is a callable, probably a closure, that instantiates the
     * service.
     *
     * @param string   $service_name   The name of the service to bind.
     * @param callable $service_loader A callable to instantiate the service.
     * @param boolean  $singleton      Whether or not the container should
     *                                 retain the instance of the class
     *                                 intantiated on the first retrieval
     *                                 attempt, thus "sharing" a singleton
     *                                 instance.
     *
     * @return void
     */
    public function bindLoader($service_name, $service_loader, $singleton=true) {
        $this->services[$service_name] = new ConcreteService(
            $service_loader->bindTo($this),
            $singleton
        );

        return $this;
    }

    /**
     * Destroy a singleton instance.
     *
     * Necessary for reloading services due to configuration and/or dataset
     * changes.
     *
     * @param string $service_name The name of the service of which to destroy
     *                             the instance.
     *
     * @return void
     */
    public function destroy($service_name) {
        unset($this->instances[$service_name]);
    }

    /**
     * Get an instance of a bound service from the container.
     *
     * If the requested service is a singleton and an instance is already
     * available, return it. If no instance is available, execute the service's
     * loader, store a reference to the instance within the container, then
     * return it to the caller.
     *
     * If the service isn't a singleton, just return a new instance.
     *
     * @param string $service_name The name of the service to retrieve.
     *
     * @return mixed The instance.
     *
     * @todo Revise error handling here. If no service was bound with the matching name, getAbstractParent will fail to
     *       handle the error. We should have getAbstractParent return null on failure to retrieve a service name, and
     *       bail out with an appropriate exception and message before proceeding with singleton detection.
     */
    public function get($service_name) {
        if ($this->isBound($service_name)) {
            $bound_service_name = $service_name;
        } else {
            $bound_service_name = $this->getAbstractParent($service_name);
        }

        if ($this->isSingleton($bound_service_name)) {
            if (!array_key_exists($service_name, $this->instances)
                    && array_key_exists($bound_service_name, $this->services)
                    && $this->services[$bound_service_name] !== NULL) {
                $this->instances[$service_name] = $this->services[$bound_service_name]->runLoader($service_name);
            }

            $instance = $this->instances[$service_name];
        } else {
            $instance = $this->services[$bound_service_name]->runLoader($service_name);
        }

        return $instance;
    }

    protected function getAbstractParent($class_name) {
        $reflector = new ReflectionClass($class_name);
        $result    = null;

        do {
            $reflector = $reflector->getParentClass();
            if ($reflector === false) {
                break;
            }

            $parent_class_name = $reflector->getName();
            if ($this->isBound($parent_class_name)) {
                $result = $parent_class_name;
            }
        } while ($reflector !== false && !isset($result));

        if ($result === null) {
            throw new UnboundServiceError($class_name);
        }

        return $result;
    }

    /**
     * Is a service of the specified name bound?
     *
     * @param string $service_name The name of the service of which to test the
     *        presence.
     *
     * @return boolean Whether or not a service of the specified name is bound.
     */
    public function isBound($service_name) {
        return array_key_exists($service_name, $this->services);
    }

    /**
     * Is the specified service a singleton?
     *
     * @param string $service_name The name of the service to look up.
     * @return boolean Whether or not the specified service is a singleton.
     */
    public function isSingleton($service_name) {
        return $this->services[$service_name]->isSingleton();
    }

    /**
     * Unbind a service.
     *
     * @param string $service_name The name of the service to unbind.
     *
     * @return void
     */
    public function unbind($service_name) {
        unset($this->services[$service_name]);
    }

    /**
     * Does the specified offset exist?
     *
     * This method should not be called directly, as it is related to the
     * ArrayAccess implementation.
     *
     * @param string $offset The name of the service of which to test the
     *        presence.
     * @return boolean Whether or not a service of the specified name is bound.
     * @see \Origin\DependencyInjection\Container::isBound() isBound(), for
     *      which this method is an alias.
     */
    public function offsetExists($offset) {
        return $this->isBound($offset);
    }

    /**
     * Get the value at the specified offset.
     *
     * This method should not be called directly, as it is related to the
     * ArrayAccess implementation.
     *
     * @param string $offset The name of the service to retrieve.
     * @return mixed The instance.
     * @see \Origin\DependencyInjection\Container::get() get(), for which this
     *      method is an alias.
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * Set the value at the specified offset.
     *
     * This method should not be called directly, as it is related to the
     * ArrayAccess implementation.
     *
     * @param string $offset The name of the service to bind.
     * @param mixed $value The instance to bind.
     * @see \Origin\DependencyInjection\Container::bindInstance()
     *      bindInstance(), for which this method is an alias.
     */
    public function offsetSet($offset, $value) {
        $this->bindInstance($offset, $value);
    }

    /**
     * Unset the value at the specified offset.
     *
     * This method should not be called directly, as it is related to the
     * ArrayAccess implementation.
     *
     * @param $string $offset The name of the service to unbind.
     * @see \Origin\DependencyInjection\Container::offsetUnset() offsetUnset(),
     *      for which this method is an alias.
     */
    public function offsetUnset($offset) {
        $this->unbind($offset);
    }
}
