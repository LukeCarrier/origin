<?php

/**
 * Database migration library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Migration;

use Origin\Database\Dbal\Schema\Table,
    Origin\Database\Dbal\Schema\TableDelta,
    Origin\Database\Dbal\Errors\NoSuchTable;

/**
 * Abstract migration base class.
 *
 * All database migrations should extend this class, which provides the basic
 * functionality required for writing upgrades.
 */
abstract class AMigration {
    /**
     * The driver of the database being operated upon.
     *
     * @var \Origin\Database\Dbal\IDriver
     */
    protected $driver;

    /**
     * Are we running in reverse?
     *
     * This will probably be removed soon... it seems unwise at best.
     *
     * @var boolean
     */
    protected $inverted;

    /**
     * An instance of the platform class which corresponds with the driver.
     *
     * @var \Origin\Database\Dbal\IPlatform
     */
    protected $platform;
    
    /**
     * The schema manager to apply operations to the database via.
     *
     * @var \Origin\Database\Dbal\schema\Manager
     */
    protected $schema_manager;

    /**
     * Initialiser.
     *
     * @param \Origin\Database\Dbal\Schema\Manager $schema_manager The schema
     *                                                             manager to
     *                                                             apply DDL
     *                                                             changes via.
     */
    final public function __construct($schema_manager) {
        $this->schema_manager = $schema_manager;
        $this->driver         = $schema_manager->getDriver();
        $this->platform       = $schema_manager->getPlatform();
    }

    /**
     * Commit changes to a table.
     *
     * @param \Origin\Database\Dbal\Schema\Table $new_table The table schema
     *                                                      object
     *                                                      representative of
     *                                                      the table schema.
     *
     * @return void
     */
    final public function commit($new_table) {
        $this->driver->beginTransaction();

        try {
            $current_table = $this->schema_manager->getTableSchema($new_table->getName());
            $delta = new TableDelta($this->platform, $current_table, $new_table);
            if ($delta->hasChanges()) {
                $this->schema_manager->applyTableDelta($delta);
            }
        } catch (NoSuchTable $e) {
            $this->schema_manager->createTable($new_table);
        }

        $this->driver->commit();
    }

    /**
     * Get a database table.
     *
     * @param string $table_name The name of the table to retrieve.
     *
     * @return \Origin\Database\Dbal\Schema\Table The table schema object.
     */
    final public function table($table_name) {
        try {
           $table = $this->schema_manager->getTableSchema($table_name);
        } catch (NoSuchTable $e) {
            $table = new Table($table_name);
        }

        return $table;
    }

    /**
     * @see IMigration->downgrade()
     */
    public function downgrade() {}

    /**
     * @see IMigration->upgrade()
     */
    public function upgrade() {}
}
