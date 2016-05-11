<?php

/**
 * Argument parser library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Console\Arguments\Errors;

use Exception;

/**
 * No such action error.
 *
 * Thrown when the command line arguments contain action parameters which have
 * not registered with the argument parser.
 */
class NoSuchAction extends Exception {
    /**
     * The requested action.
     *
     * @var string
     */
    protected $action;

    /**
     * Initialiser.
     *
     * @param string $action The requested action.
     *
     * @override \Exception
     */
    public function __construct($action) {
        $this->action = $action;
    }

    /**
     * Return a string representation of the exception.
     *
     * @return string Formatted string representation.
     *
     * @override \Exception
     */
    public function __toString() {
        return __NAMESPACE__ . '\\' . __CLASS__ . ": action '{$this->action}' not registered";
    }

    /**
     * Get the requested action.
     *
     * @return string The requested action.
     */
    public function getAction() {
        return $this->action;
    }
}
