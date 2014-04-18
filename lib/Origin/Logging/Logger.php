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

use Origin\Logging\Errors\InvalidLevel as InvalidLevelError;

/**
 * Logger.
 *
 * The logger behaves as an intermediary between log messages, processors and targets:
 * * A message is an individual record in a log, representative of an event, error condition or simple debug output.
 *   Messages are each tagged with a specific level, which represents their severity.
 * * At each log level, developers may bind a series of log targets. These targets might be output streams, databases or
 *   even remote web services. Targets receive only the messages with a level at or above their assigned level for
 *   processing.
 * * Each log target will accept a log message formatter, which will be utilised to generate data suitable for recording
 *   from all of the contextual information about the message event.
 * * In order to facilitate the addition of extra contextual information, the logger allows developers to register
 *   processors. Unlike targets, processors are not tied to a specific level and will be executed for all messages.
 *
 * The logging library is designed to be extensible. If desired, application developers may provide their own log
 * targets and processors by implementing the ITarget and IProcessor interfaces respectively. There are no namespacing
 * constraints imposed upon developers, and by extension there's no need to patch Origin itself.
 */
class Logger {
    /**
     * Level: debug.
     *
     * @var integer
     */
    const LEVEL_DEBUG = 1;

    /**
     * Level: information.
     *
     * @var integer
     */
    const LEVEL_INFORMATION = 2;

    /**
     * Level: notice.
     *
     * @var integer
     */
    const LEVEL_NOTICE = 3;

    /**
     * Level: warning.
     *
     * @var integer
     */
    const LEVEL_WARNING = 4;

    /**
     * Level: error.
     *
     * @var integer
     */
    const LEVEL_ERROR = 5;

    /**
     * Level: critical.
     *
     * @var integer
     */
    const LEVEL_CRITICAL = 6;

    /**
     * Level: alert.
     *
     * @var integer
     */
    const LEVEL_ALERT = 7;

    /**
     * Level: emergency.
     *
     * @var integer
     */
    const LEVEL_EMERGENCY = 8;

    /**
     * Log levels.
     *
     * @var string[]
     */
    protected static $LEVELS;

    /**
     * Has the class initialised additional state?
     *
     * @var boolean
     */
    protected static $initialised;

    /**
     * Instance name.
     *
     * @var string
     */
    protected $name;

    /**
     * Registered message processors.
     *
     * @var \Origin\Logging\IProcessor[]
     */
    protected $processors;

    /**
     * Registered log targets.
     *
     * @var \Origin\Logging\ITarget[][]
     */
    protected $targets;

    /**
     * Return the name of a level.
     *
     * @param integer $level One of the LEVEL_* constants, representative of a logging level.
     *
     * @return string The name of the logging level.
     */
    public static function getLevelName($level) {
        static::maybeInitialise();

        if (array_key_exists($level, static::$LEVELS)) {
            return static::$LEVELS[$level];
        } else {
            throw new InvalidLevelError($level);
        }
    }

    /**
     * Initialise additional class state.
     *
     * @return void
     */
    protected static function initialise() {
        static::$LEVELS = [
            static::LEVEL_DEBUG       => 'debug',
            static::LEVEL_INFORMATION => 'information',
            static::LEVEL_NOTICE      => 'notice',
            static::LEVEL_WARNING     => 'warning',
            static::LEVEL_ERROR       => 'error',
            static::LEVEL_CRITICAL    => 'critical',
            static::LEVEL_ALERT       => 'alert',
            static::LEVEL_EMERGENCY   => 'emergency',
        ];
    }

    /**
     * Initialise additional class state if initialisation has not yet taken place.
     *
     * @return void
     */
    protected static function maybeInitialise() {
        if (!static::$initialised) {
            static::initialise();
        }

        static::$initialised = true;
    }

    /**
     * Initialiser.
     *
     * @param string                      $name       The name of the logger instance.
     * @param \Origin\Logging\ITarget[][] $targets    Log targets, in a multidimensional array:
     *                                                [$level => [$target1, $target2, ...], ...]
     * @param \Origin\Logging\IProcessor  $processors Log processors to be executed on every log message.
     */
    public function __construct($name, $targets=[], $processors=[]) {
        static::maybeInitialise();

        $this->name       = $name;
        $this->targets    = [];
        $this->processors = [];

        foreach (static::$LEVELS as $level => $name) {
            $this->targets[$level] = [];

            if (array_key_exists($level, $targets)) {
                foreach ($targets[$level] as $target) {
                    $this->addTarget($level, $target);
                }
            }
        }

        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    /**
     * Add a processor to be executed at all error levels.
     *
     * @param \Origin\Logging\IProcessor $processor The processor to add.
     *
     * @return void
     */
    public function addProcessor(IProcessor $processor) {
        $this->processors[] = $processor;
    }

    /**
     * Add a log target at a specific level.
     *
     * @param integer                 $level  The level at which to attach the target.
     * @param \Origin\Logging\ITarget $target The target to be attached.
     *
     * @return void
     */
    public function addTarget($level, ITarget $target) {
        $this->targets[$level][] = $target;
    }

    /**
     * Get the name of the logger.
     *
     * @return string The name of the logger.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Log a message at the specified level.
     *
     * @param integer $level   One of the LEVEL_* constants describing a log level.
     * @param string  $message The message to log.
     *
     * @return void
     */
    public function log($level, $message) {
        static::maybeInitialise();

        $message = new Message($level, $message);

        foreach ($this->processors as $processor) {
            $processor->process($message);
        }

        foreach (array_keys(static::$LEVELS) as $current_level) {
            if ($current_level > $level) {
                continue;
            }

            foreach ($this->targets[$current_level] as $target) {
                $target->record($message);
            }
        }
    }

    /**
     * Log a debug message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function debug($message) {
        $this->log(static::LEVEL_DEBUG, $message);
    }

    /**
     * Log a information message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function information($message) {
        $this->log(static::LEVEL_INFORMATION, $message);
    }

    /**
     * Log a notice message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function notice($message) {
        $this->log(static::LEVEL_NOTICE, $message);
    }

    /**
     * Log a warning message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function warning($message) {
        $this->log(static::LEVEL_WARNING, $message);
    }

    /**
     * Log a error message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function error($message) {
        $this->log(static::LEVEL_ERROR, $message);
    }

    /**
     * Log a critical message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function critical($message) {
        $this->log(static::LEVEL_CRITICAL, $message);
    }

    /**
     * Log a alert message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function alert($message) {
        $this->log(static::LEVEL_ALERT, $message);
    }

    /**
     * Log a emergency message.
     *
     * @param string $message The message to be recorded.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function emergency($message) {
        $this->log(static::LEVEL_EMERGENCY, $message);
    }
}
