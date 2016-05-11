<?php

/**
 * Database migration task.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@cfx.me>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\Tasks;
use Origin\Console\Task\ITask;

class Migrate implements ITask {
    /**
     * The migration manager responsible for the DDL changes.
     *
     * @var \Origin\Database\Dbal\Migration\Manager
     */
    protected $migration_manager;

    /**
     * Initialiser.
     *
     * @param \Origin\Database\Dbal\Migration\Manager $migration_manager The migration manager
     *                                                                   responsible for the DDL
     *                                                                   changes.
     */
    public function __construct($migration_manager) {
        $this->migration_manager = $migration_manager;
    }

    /**
     * Run the migration.
     *
     * @param string $direction Which direction the migration should run in, and
     *                          to which version.
     *
     * @return boolean Whether or not the migration was successful.
     */
    public function run($direction) {
        return $this->migration_manager->migrate($direction);
    }
}
