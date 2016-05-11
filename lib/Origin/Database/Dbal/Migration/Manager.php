<?php

/**
 * Database migration manager.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@cfx.me>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Migration;

use Origin\Database\Dbal\Schema\Column,
    Origin\Database\Dbal\Schema\Constraint,
    Origin\Database\Dbal\Schema\Table,
    PDOException;

/**
 * Database migration manager.
 *
 * The database migration manager aims to simplify the maintenance of your
 * application's database schema.
 */
class Manager {
    /**
     * Migration down direction.
     *
     * @var string
     */
    const DIRECTION_DOWN = 'down';

    /**
     * Migration up direction.
     *
     * @var string
     */
    const DIRECTION_UP = 'up';

    /**
     * Database.
     *
     * @var origin\database\IDriver
     */
    protected $driver;

    /**
     * Migration iterator.
     *
     * @var Iterator
     */
    protected $iterator;

    /**
     * Migration table.
     *
     * @var string
     */
    protected $migration_table;

    /**
     * Platform.
     *
     * @var \Origin\Database\Dbal\IPlatform
     */
    protected $platform;

    /**
     * Post-apply callback.
     *
     * @var callable
     */
    protected $post_apply_callback;

    /**
     * Schema manager.
     *
     * @var \Origin\Database\Dbal\Schema\Manager
     */
    protected $schema_manager;

    /**
     * Initialiser.
     *
     * @param \Origin\Database\Dbal\IDriver                      $driver          Database driver.
     * @param \Origin\Database\Dbal\IPlatform                    $platform        Database platform.
     * @param \Origin\Database\Dbal\Migration\IMigrationIterator $iterator        Migration iterator.
     * @param SchemaManager                                      $schema_manager  The scheme manager to
     *                                                                            use for DDL operations.
     * @param string                                             $migration_table The name of the table
     *                                                                            that should contain the
     *                                                                            migration status
     *                                                                            information.
     */
    public function __construct($driver, $platform, $iterator,
                                $schema_manager,
                                $migration_table='origin_migration') {
        $this->driver          = $driver;
        $this->platform        = $platform;
        $this->iterator        = $iterator;
        $this->schema_manager  = $schema_manager;
        $this->migration_table = $migration_table;
    }

    /**
     * Run the migration.
     *
     * @param string $direction Indicates the direction of the migration; one of
     *        the DIRECTION_* constants.
     *
     * @return void
     */
    public function migrate($direction) {
        $this->setupMigrationTable();

        $record_sql = $this->platform->getInsertSql($this->migration_table, array(
            'name',
            'created_at',
            'applied_at',
        ));
        $record_statement = $this->driver->prepare($record_sql);

        // @todo this code is crap, so make a pretty later
        $executed_sql = 'SELECT COUNT(' . $this->platform->prepareColumnName('id') . ') '
                      . 'FROM ' . $this->platform->prepareTableName($this->migration_table) . ' '
                      . 'WHERE ' . $this->platform->prepareColumnName('name') . ' = ? '
                      .     'AND ' . $this->platform->prepareColumnName('created_at') . ' = ?';

        foreach ($this->iterator as $migration) {
            // Skip it if it's already been applied :D
            //continue;

            // not all migration iterators rely on the migration manager's class
            // file inclusion
            $migration_file = $migration->getMigrationPathname();
            if ($migration_file !== NULL) {
                require_once $migration_file;
            }

            var_dump($migration->getMigrationClass()); die;

            $migration_class            = $migration->getMigrationClass();
            $migration_instance         = new $migration_class($this->schema_manager);
            $migration_name             = $migration->getMigrationName();
            $migration_creation_time    = $migration->getMigrationCreationTime();
            $migration_application_time = time();

            switch ($direction) {
                case static::DIRECTION_DOWN:
                    $result = $migration_instance->downgrade();
                    break;

                case static::DIRECTION_UP:
                    $result = $migration_instance->upgrade();
                    break;
            }

            try {
                $record_statement->execute(array(
                    $migration_name,
                    $migration_time,
                ));
            } catch (PDOException $e) {
                /* For database drivers that cache the database schema and don't
                 * correctly expire it when DDL operations occur, we provide an
                 * alternate code path which avoids using (cacheable) prepared
                 * statements. At the present time, only the PDO SQLite driver
                 * is known to exhibit this behaviour:
                 *
                 *     https://bugs.php.net/bug.php?id=43942 */

                $sql = $this->platform->getInsertSql($this->migration_table, array(
                    'name',
                    'time',
                ), $this->driver, array(
                    $migration_name,
                    $migration_time,
                ));
                $this->driver->exec($sql);
            }
        }
    }

    /**
     * Set up migration table.
     *
     * Set up the migration metadata table.
     *
     * @return void
     */
    protected function setupMigrationTable() {
        if (!$this->schema_manager->hasTable($this->migration_table)) {
            $table = new Table($this->migration_table);
            $table->addColumn(new Column('id', Column::TYPE_INTEGER, array(
                'auto_increment' => true,
                'length'         => 10,
            ), array(
                new Constraint(Constraint::TYPE_PRIMARYKEY),
                new Constraint(Constraint::TYPE_NOTNULL),
            )));
            $table->addColumn(new Column('name', Column::TYPE_STRING, array(
                'length' => 256,
            ), array(
                new Constraint(Constraint::TYPE_NOTNULL),
            )));
            $table->addColumn(new Column('created_at', Column::TYPE_TIMESTAMP,
                              array(), array(
                new Constraint(Constraint::TYPE_NOTNULL),
            )));
            $table->addColumn(new Column('applied_at', Column::TYPE_TIMESTAMP,
                              array(), array(
                new Constraint(Constraint::TYPE_NOTNULL),
            )));

            $this->schema_manager->createTable($table);
        }
    }
}
