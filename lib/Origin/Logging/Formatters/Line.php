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

namespace Origin\Logging\Formatters;

use DateTime,
    Origin\Logging\IFormatter,
    Origin\Logging\Logger,
    Origin\Logging\Message,
    Origin\Util\StringUtil;

/**
 * Format messages for lines in a log file.
 */
class Line implements IFormatter {
    /**
     * Default message format.
     *
     * @var string
     */
    const DEFAULT_MESSAGE_FORMAT = "[{time} {level}] {message}\n";

    /**
     * Message format.
     *
     * @var string
     */
    protected $message_format;

    /**
     * Initialiser.
     *
     * @param string $message_format An optional format string to override the default. If not specified, defaults to
     *                               DEFAULT_MESSAGE_FORMAT.
     */
    public function __construct($message_format=null) {
        $this->message_format = ($message_format === null) ? static::DEFAULT_MESSAGE_FORMAT : $message_format;
    }

    /**
     * Handle a log message.
     *
     * @param \Origin\Logging\Message $message The message to handle.
     *
     * @return void
     *
     * @override \Origin\Logging\IFormatter
     */
    public function format(Message $message) {
        $parameters = [
            'level'   => Logger::getLevelName($message->getLevel()),
            'message' => $message->getMessage(),
            'time'    => $this->formatTime($message->getTime()),
        ];

        return StringUtil::format($this->message_format, $parameters);
    }

    /**
     * Format a loggable date from a timestamp.
     *
     * This method exists to workaround PHP's date functions, none of which correctly handle microseconds.
     *
     * @param float $time The timestamp, in the format {seconds}.{microseconds}.
     *
     * @return string The formatted timestamp.
     */
    protected function formatTime($time) {
        list($seconds, $microseconds) = explode('.', (string) $time);

        return date('d/m/Y H:i:s.', $seconds) . $microseconds;
    }
}
