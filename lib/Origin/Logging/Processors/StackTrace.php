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

namespace Origin\Logging\Processors;

use Origin\Logging\IProcessor,
    Origin\Logging\Message;

/**
 * Add a stack trace to the context information.
 */
class StackTrace implements IProcessor {
    /**
     * Process an incoming message.
     *
     * @param \Origin\Logging\Message $message The log message to add the state information to.
     *
     * @return void
     *
     * @override \Origin\Logging\IProcessor
     */
    public function process(Message $message) {
        $trace = debug_backtrace();
        $message->setContextKey('stack_trace', $trace);
    }
}
