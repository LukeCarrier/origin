<?php

/**
 * Serialisation utility API.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Util\Errors;

use Exception;

/**
 * File access exception.
 *
 * Raised when attempts to read from/write to filesystems fail.
 */
class File extends Exception {
    /**
     * Access type: read.
     *
     * @var integer
     */
    const ACCESS_READ = 0;

    /**
     * Access type: write.
     *
     * @var integer
     */
    const ACCESS_WRITE = 1;

    /**
     * Target type: directory.
     *
     * @var integer
     */
    const TYPE_DIRECTORY = 0;

    /**
     * Target type: file.
     *
     * @var integer
     */
    const TYPE_FILE = 2;

    /**
     * The path to the target file or directory.
     *
     * @var string
     */
    protected $path;

    /**
     * Additional information to output along with the error condition.
     *
     * @var string
     */
    protected $information;

    /**
     * Initialiser.
     *
     * @param integer    $code        A bitmask value representing the error condition, generated using the ACCESS_* and
     *                                TYPE_* constants.
     * @param string     $path        The path to the file the access atempt was made upon.
     * @param string     $information An optional message containing additional debugging information,
     * @param \Exception $previous    Optional previous exception, if we were raised during the handling of another
     *                                exception condition.
     *
     * @override \Exception
     */
    public function __construct($code, $path, $information=null, $previous=null) {
        $this->code        = $code;
        $this->path        = $path;
        $this->information = $information;
        $this->previous    = $previous;

        parent::__construct((string) $this, $this->code, $previous);
    }

    /**
     * Return a string representation of the exception.
     *
     * @return string Formatted string representation.
     *
     * @override \Exception
     */
    public function __toString() {
        $format = $this->getMessageFormatString();

        $action = ($this->getAccessType() === static::ACCESS_READ)    ? 'read from' : 'write to';
        $type   = ($this->getPathType()   === static::TYPE_DIRECTORY) ? 'directory' : 'file';

        return ($this->message === null) ? sprintf($format, $action, $type, $this->path)
                                         : sprintf($format, $action, $type, $this->path, $this->message);
    }

    /**
     * Get the access type.
     *
     * @return integer One of the ACCESS_* constants.
     */
    public function getAccessType() {
        return ($this->code & static::ACCESS_WRITE) === static::ACCESS_WRITE;
    }

    /**
     * Get format string for the specified code.
     *
     * @return string The message's format string.
     */
    protected function getMessageFormatString() {
        $format = 'failed to %s %s "%s"';
        if ($this->information !== null) {
            $format .= ': %s';
        }

        return $format;
    }

    /**
     * Get the path type.
     *
     * @return integer One of the TYPE_* constants.
     */
    public function getPathType() {
        return ($this->code & static::TYPE_FILE) === static::TYPE_FILE;
    }
}
