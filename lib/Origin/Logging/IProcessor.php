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
 * Message processor.
 *
 * Message processors are utilised in Origin's logging library to gather and add additional contextual information to
 * log messages. Log targets handling the recording of the log messages can later store this extended information.
 */
interface IProcessor {
    /**
     * Process an incoming message.
     *
     * @param \Origin\Logging\Message $message The log message to add the state information to.
     *
     * @return void
     */
    public function process(Message $message);
}
