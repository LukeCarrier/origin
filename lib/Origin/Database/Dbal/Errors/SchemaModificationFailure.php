<?php

/**
 * DBAL generator interface.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Errors;
use Exception;

/**
 * Schema modification failure exception.
 */
class SchemaModificationFailure extends Exception {
    /**
     * SQLSTATE code.
     *
     * @var string
     */
    protected $sqlstate_code;

    /**
     * Driver code.
     *
     * @var string
     */
    protected $driver_code;

    /**
     * Driver message.
     *
     * @var string
     */
    protected $driver_message;

    /**
     * Initialiser.
     *
     * @param integer $sqlstate_code  SQLSTATE code.
     * @param integer $driver_code    Driver code.
     * @param string  $driver_message Driver message.
     */
    public function __construct($sqlstate_code, $driver_code,
                                $driver_message) {
        $this->sqlstate_code  = $sqlstate_code;
        $this->driver_code    = $driver_code;
        $this->driver_message = $driver_message;
    }

    /**
     * Get SQLSTATE code.
     *
     * @return integer The SQLSTATE code.
     */
    public function getSqlstateCode() {
        return $this->sqlstate_code;
    }

    /**
     * Get driver code.
     *
     * @return integer The driver code.
     */
    public function getDriverCode() {
        return $this->driver_code;
    }

    /**
     * Get driver message.
     *
     * @return string The driver message.
     */
    public function getDriverMessage() {
        return $this->driver_message;
    }
}
