<?php

/**
 * DBAL error.
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
 * No such table exception.
 */
class NoSuchTable extends Exception {
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table_name;

    /**
     * Initialiser.
     *
     * @param string $table_name The name of the table.
     */
    public function __construct($table_name) {
        $this->table_name = $table_name;
    }

    /**
     * @override
     */
    public function __toString() {
        return __NAMESPACE__ . '\\' . __CLASS__
                 . ": table '{$this->table_name}' does not exist";
    }

    /**
     * Get the name of the table.
     *
     * @return string The name of the table.
     */
    public function getTableName() {
        return $this->table_name;
    }
}
