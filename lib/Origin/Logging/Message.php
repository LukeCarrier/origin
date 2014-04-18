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
 * Log message.
 *
 * Represents an individual log message.
 */
class Message {
    /**
     * Additional contextual information.
     *
     * @var mixed[]
     */
    protected $context;

    /**
     * Log level.
     *
     * @var integer
     */
    protected $level;

    /**
     * Log message.
     *
     * @var string
     */
    protected $message;

    /**
     * Log time.
     *
     * @var float
     */
    protected $time;

    /**
     * Initialiser.
     *
     * @param integer $level   Log level.
     * @param string  $message Log message.
     * @param mixed[] $context Additional contextual information.
     */
    public function __construct($level, $message, $context=[]) {
        $this->level   = $level;
        $this->message = $message;
        $this->context = $context;
        $this->time    = microtime(true);
    }

    /**
     * Get the additional contextual information.
     *
     * @return mixed[] The additional contextual information.
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * Set contextual information.
     *
     * @param string $key   The key to add or overwrite.
     * @param mixed  $value The new value of the specified key.
     *
     * @return void
     */
    public function setContextKey($key, $value) {
        $this->context[$key] = $value;
    }

    /**
     * Get the log level.
     *
     * @return integer The log level.
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * Get the log level name.
     *
     * @return string The log level name.
     */
    public function getLevelName() {
        return Logger::getLevelName($this->level);
    }

    /**
     * Get the log message.
     *
     * @return string The log message.
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Get the log time.
     *
     * @return float The log time.
     */
    public function getTime() {
        return $this->time;
    }
}
