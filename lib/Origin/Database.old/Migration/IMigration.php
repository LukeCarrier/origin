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

/**
 * Individual database migration.
 */
interface IMigration {
    /**
     * Define manual downgrade.
     *
     * If not defined, Origin will make an educated guess based on the content
     * of the upgrade migration and the database schema at the time of the
     * downgrade.
     */
    public function downgrade();

    /**
     * Define manual upgrade.
     */
    public function upgrade();
}
