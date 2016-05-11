<?php

/**
 * DBAL driver manager.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal;
use Origin\Database\Dbal\Errors\NoSuchAdapter;

/**
 * Database adapter manager.
 *
 * The DBAL adapter manager acts as an intermediary layer, preventing
 * applications from having to manually initialise all of the components of a
 * the adapters. Simply instantiate the adapter class for the RDBMS you
 * require, then call {@link addAdapter} to register it.
 */
class AdapterManager {
    /**
     * Registered adapters.
     *
     * @var \Origin\Database\Dbal\IAdapter[]
     */
    protected $adapters;

    /**
     * Register a adapter with the adapter manager.
     *
     * @param \Origin\Database\Dbal\IAdapter $adapter The adapter to register.
     */
    public function addAdapter($adapter) {
        $this->adapters[$adapter->getName()] = $adapter;
    }

    /**
     * Create a new connection.
     *
     * @param string $dsn The data source name.
     *
     * @return \Origin\Database\Dbal\IDriver              The new connection's
     *                                                    driver instance.
     * @throws \Origin\Database\Dbal\Errors\NoSuchAdapter When attempting to
     *                                                    connect using a
     *                                                    driver for an unloaded
     *                                                    adapter.
     */
    public function connect($dsn) {
        list($adapter_name, ) = explode(':', $dsn, 2);

        if (!array_key_exists($adapter_name, $this->adapters)) {
            throw new NoSuchAdapter($adapter_name);
        }

        $driver = $this->adapters[$adapter_name]->connect($dsn);
        $driver->setAdapterName($adapter_name);

        return $driver;
    }

    /**
     * Get a database platform.
     *
     * @param \Origin\Database\Dbal\IDriver $driver The database driver
     *                                              instance, as returned by
     *                                              connect().
     */
    public function getPlatform($driver) {
        $adapter_name = $driver->getAdapterName();
        return $this->adapters[$adapter_name]->getPlatform($driver);
    }
}
