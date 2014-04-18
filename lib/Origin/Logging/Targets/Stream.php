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

namespace Origin\Logging\Targets;

use Origin\Logging\ATarget,
    Origin\Logging\ITarget,
    Origin\Logging\Message,
    Origin\Logging\Formatters\Line as LineFormatter;

/**
 * Stream log target.
 *
 * The stream log target enables logging to PHP streams, including:
 *
 * * files
 * * output streams
 */
class Stream extends ATarget implements ITarget {
    /**
     * Whether or not to close the stream on destruct.
     *
     * @var boolean
     */
    protected $close_on_destruct;

    /**
     * Open file resource.
     *
     * @var resource
     */
    protected $stream;

    /**
     * Initialiser.
     *
     * @param resource                   $stream            An open, writable file or stream resource.
     * @param \Origin\Logging\IFormatter $formatter         The formatter.
     * @param boolean                    $close_on_destruct True if the stream target should attempt to close the file
     *                                                      upon its destruction, else false.
     *
     * @override \Origin\Logging\ATarget
     */
    public function __construct($stream, $formatter=null, $close_on_destruct=true) {
        parent::__construct($formatter);

        $this->stream            = $stream;
        $this->close_on_destruct = $close_on_destruct;
    }

    /**
     * Destructor.
     *
     * If were told to do so, we should close the file handle for the stream target.
     */
    public function __destruct() {
        if ($this->close_on_destruct) {
            fclose($this->stream);
        }
    }

    /**
     * Get the default formatter.
     *
     * @return \Origin\Logging\Formatters\Line
     *
     * @override \Origin\Logging\ITarget
     */
    public function getDefaultFormatter() {
        return new LineFormatter();
    }

    /**
     * Handle a log message.
     *
     * @param \Origin\Logging\Message $message The message to handle.
     *
     * @return void
     *
     * @override \Origin\Logging\ITarget
     */
    public function record(Message $message) {
        fwrite($this->stream, $this->formatter->format($message));
    }
}
