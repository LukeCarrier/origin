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

use Origin\Routing\IControllerFactory;

/**
 * Direct instantiation controller factory.
 *
 * As the name implies, this controller factory blindly instantiates the named
 * class with no consideration for its parameters.
 */
class DirectInstantiation implements IControllerFactory {
    /**
     * @override
     */
    public function get($class_name) {
        return new $class_name();
    }
}
