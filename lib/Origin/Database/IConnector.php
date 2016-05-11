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

namespace Origin\Database;

/**
 * Database connector interface.
 *
 * Origin breaks support for different relational database servers into connector libraries. Each connector is a
 * combination of:
 *
 * * A platform, which abstracts over platform-specific behaviour and dialects.
 * * A schema manager, which provides access to and facilitates the modification of the underlying database schema.
 */
interface IConnector {
    /**
     * Create a new connection using the given URL.
     *
     * @param string $dsn The URL containing information about the database's corresponding data source.
     *
     * @return \Origin\Database\IDriver The connection's driver instance.
     */
    public function connect($dsn);

    /**
     * Get the connector's name.
     *
     * @return string The name of the connector.
     */
    public function getName();

    /**
     * Get a platform object for SQL query generation.
     *
     * @param \Origin\Database\IDriver $driver The driver object to attach to the platform.
     *
     * @return \Origin\Database\IPlatform The platform object.
     */
    public function getPlatform($driver);
}
