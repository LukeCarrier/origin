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

namespace Origin\Routing;

/**
 * Controller factory interface.
 *
 * In Origin's routing library, the controller factory is the component
 * responsible for the instantiation of controller classes necessary in the
 * process of responding to the request.
 */
interface IControllerFactory {
    /**
     * Get an instance of the specified controller.
     *
     * @param string $class_name The name of the class to instantiate.
     *
     * @return mixed The controller instance.
     */
    public function get($class_name);
}
