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
 * Log formatter interface.
 *
 * In Origin's logging library, a log formatter is used to fashion a recordable form of a log message event. The output
 * format will vary from formatter to formatter -- a stream target will likely require string-formatted messages,
 * whereas database-backed targets may require arrays or record objects.
 */
interface IFormatter {
    /**
     * Handle a log message.
     *
     * @param \Origin\Logging\Message $message The message to handle.
     *
     * @return mixed A form of log message
     */
    public function format(Message $message);
}
