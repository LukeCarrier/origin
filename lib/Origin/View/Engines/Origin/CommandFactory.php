<?php

/**
 * Origin view engine command factory.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;

use Origin\View\Engines\Origin\Errors\NoSuchCommand;

/**
 * View command factory.
 *
 * The new home for stupidly placed logic in the Block class.
 */
class CommandFactory {
    /**
     * Registered commands.
     */
    protected $commands;

    /**
     * Register a command class with the loader.
     */
    public function addCommand($name, $handler) {
        $this->commands[$name] = $handler;
    }

    /**
     * Retrieve a command class.
     */
    public function getCommand($name) {
        if (!array_key_exists($name, $this->commands)) {
            throw new NoSuchCommand($name);
        }

        return $this->commands[$name];
    }
}
