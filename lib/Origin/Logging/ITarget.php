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

namespace Origin\Logging;

/**
 * Log target interface.
 *
 * In Origin's logging library, a log target provides an interface between the application and a record of logged
 * messages, either local or remote.
 */
interface ITarget {
    /**
     * Get the default formatter.
     *
     * @return \Origin\Logging\IFormatter
     */
    public function getDefaultFormatter();

    /**
     * Handle a log message.
     *
     * @param \Origin\Logging\Message $message The message to handle.
     *
     * @return void
     */
    public function record(Message $message);
}
