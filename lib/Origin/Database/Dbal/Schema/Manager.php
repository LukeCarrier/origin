<?php

/**
 * DBAL schema manager.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Schema;

use Origin\Database\Dbal\Errors\NoSuchTable,
    Origin\Database\Dbal\Errors\SchemaModificationFailure,
    Origin\Database\Dbal\Schema\Table;

/**
 * Schema manager.
 *
 * The schema manager is responsible for all schema amendments to a database by
 * an Origin application. It coordinates all changes through the connected
 * driver and corresponding query platform.
 */
class Manager {
    /**
     * Connected driver.
     *
     * @var IDriver
     */
    protected $driver;
    
    /**
     * SQL query platform.
     *
     * @var IGenerator
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param IDriver $driver The connected driver.
     * @param IGenerator $platform The SQL query platform.
    */
    public function __construct($driver, $platform) {
        $this->driver   = $driver;
        $this->platform = $platform;
    }

    /**
     * Apply a table delta.
     *
     * @param TableDelta $table_delta The table delta object to format and apply
     *        to the schema.
     *
     * @return void
     */
    public function applyTableDelta($table_delta) {
        $sql = $this->platform->getTableDeltaSql($table_delta);
        $this->driver->exec($sql);
    }

    /**
     * Create a table.
     *
     * @todo Throw exceptions on error!
     *
     * @param Table $table The table schema object.
     *
     * @return void
     */
    public function createTable($table) {
        $sql = $this->platform->getCreateTableSql($table);
        $this->driver->exec($sql);
    }

    /**
     * Get the DBAL driver DDL queries are being applied against.
     *
     * @return \Origin\Database\Dbal\IDriver The driver instance.
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Get the DBAL platform being used for query generation.
     *
     * @return \Origin\Database\Dbal\IPlatform The platform instance.
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * Get a table schema object representative of the specified table.
     *
     * @param string $table_name
     *
     * @return \origin\database\schema\Table
     * @throws \origin\database\errors\NoSuchTable
     *
     * @todo Table-level (i.e. multi-field) constraints
     */
    public function getTableSchema($table_name) {
        if ($this->hasTable($table_name)) {
            $columns = $this->platform->getTableColumns($this->driver,
                                                        $table_name);
            //$constraints = $this->platform->getTableConstraints($table_name);

            return new Table($table_name, $columns);
        } else {
            throw new NoSuchTable($table_name);
        }
    }

    /**
     * Check whether the specified table exists.
     *
     * @param string $table_name The table's name.
     * @return boolean True if the table exists, else false.
     */
    public function hasTable($table_name) {
        $statement = $this->driver->prepare($this->platform->getTableExistsSql());
        $statement->bindParam(1, $table_name);
        $statement->execute();

        $record = $statement->fetchObject();
        $statement->closeCursor();

        return (bool) $record->count;
    }

    /**
     * Shorthand function for throwing errors from SQLSTATE conditions.
     *
     * @throws \origin\database\errors\SchemaModificationFailure always!
     *
     * @param array $error_info The error information, as returned by the
     *                          driver's errorInfo() method.
     */
    public function throwException($error_info) {
        throw new SchemaModificationFailure($error_info[0], $error_info[1],
                                            $error_info[2]);
    }
}
