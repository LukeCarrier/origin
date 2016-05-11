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

/**
 * Database migration iterator interface.
 *
 * Database migration iterators are responsible for sourcing migrations from the
 * filesystem. This interfaces enables 
 */
interface IMigrationIterator {
    /**
     * Get migration class.
     *
     * @return string The migration class.
     */
    public function getMigrationClass();

    /**
     * Get migration name.
     *
     * @return string The migration name.
     */
    public function getMigrationName();

    /**
     * Get migration time.
     *
     * @return string The migration time.
     */
    public function getMigrationCreationTime();

    /**
     * Get the path of the containing file for include.
     *
     * @return null|string Null return values indicate that no attempt to
     *                     include the file should be made. Any string value
     *                     will be interpreted as a filename and require_once'd.
     */
    public function getMigrationPathname();
}
