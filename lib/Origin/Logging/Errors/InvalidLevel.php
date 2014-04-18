<?php

/**
 * Application logging API.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Logging\Errors;

use Exception;

/**
 * Invalid log level exception.
 *
 * This exception is thrown when a developer attempts to perform an operation at an invalid (nonexistent) log level.
 */
class InvalidLevel extends Exception {
    /**
     * Initialiser.
     *
     * @param integer    $level    The log level (one of the LEVEL_* constants on the Logger class).
     * @param \Exception $previous Optional previous exception, if we were raised during the handling of another
     *                             exception condition.
     *
     * @override \Exception
     */
    public function __construct($level, $previous=null) {
        $this->level    = $level;
        $this->previous = $previous;

        parent::__construct((string) $this, $this->level, $previous);
    }

    /**
     * Return a string representation of the exception.
     *
     * @return string Formatted string representation.
     *
     * @override \Exception
     */
    public function __toString() {
        return sprintf('level %d is not known', $this->level);
    }

    /**
     * Get the log level.
     *
     * @return integer The log level.
     */
    public function getLevel() {
        return $this->level;
    }
}
