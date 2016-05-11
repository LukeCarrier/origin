<?php

/**
 * Database abstraction layer.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database;

use Origin\Database\Errors\NoSuchConnector,
    Origin\Database\Configuration;

/**
 * Database connector manager.
 */
class ConnectorManager {
    /**
     * Registered connectors.
     *
     * @var \Origin\Database\IConnector[]
     */
    protected $connectors;

    /**
     * Register a connector with the connector manager.
     *
     * @param \Origin\Database\IConnector $connector The connector to register.
     */
    public function addConnector($connector) {
        $this->connectors[$connector->getName()] = $connector;
    }

    /**
     * Create a new connection.
     *
     * @param \Origin\Database\Configuration $configuration Configuration parameters for the new connection.
     *
     * @return \Origin\Database\IDriver The new connection's driver instance.
     *
     * @throws \Origin\Database\Errors\NoSuchconnector When attempting to connect using a driver for an unregistered
     *                                                 connector.
     */
    public function connect($configuration) {
        list($connector_name, ) = explode(':', $dsn, 2);

        if (!array_key_exists($connector_name, $this->connectors)) {
            throw new NoSuchconnector($connector_name);
        }

        $driver = $this->connectors[$connector_name]->connect($dsn);
        $driver->setConnectorName($connector_name);

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
        $connector_name = $driver->getconnectorName();
        return $this->connectors[$connector_name]->getPlatform($driver);
    }
}
