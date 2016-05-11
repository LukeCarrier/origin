<?php

/**
 * DBAL database driver library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal;

/**
 * Database adapter interface.
 *
 * Adapter objects glue together the many different components of a supported
 * database type.
 */
interface IAdapter {
    /**
     * Create a new connection using the given URL.
     *
     * @param string $uri The URL containing information about the database's
     *                    corresponding data source.
     *
     * @return \origin\database\IDriver The connection's driver instance.
     */
    public function connect($uri);

    /**
     * Get the adapter's name.
     *
     * @return string The name of the adapter.
     */
    public function getName();

    /**
     * Get a platform object for SQL query generation.
     *
     * @param \origin\database\IDriver $driver The driver object to attach to
     *        the platform.
     *
     * @return \origin\database\IPlatform The platform object.
     */
    public function getPlatform($driver);
}
