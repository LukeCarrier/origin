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
 * DBAL driver interface.
 *
 * DBAL driver objects form the heart of database connectivity within Origin
 * applications. They wrap the native database drivers within PHP to provide
 * a consistent API for developers.
 *
 * @see PDO The API below is based upon PHP's own PDO API; for details on
 *      parameters you should refer to the PHP documentation.
 */
interface IDriver {
    /**
     * Connect to the database.
     *
     * @param string $uri The URI of the database to connect to. This should be
     *        in a format which is parsable with parse_url, as it'll be
     *        translated into an RDBMS-specific data source name by the driver
     *        implementation.
     * @param \origin\database\IPlatform $platform Platform object containing
     *        all RDBMS-specific implementations.
     */
    //public function __construct($uri, $platform_class);

    /**
     * Begin a transaction.
     */
    //public function beginTransaction();
    
    /**
     * Commit the current transaction.
     */
    //public function commit();
    
    /**
     * Get the last error code.
     */
    //public function errorCode();
    
    /**
     * Get detiled error information.
     */
    //public function errorInfo();
    
    /**
     * Execute a statement.
     */
    //public function exec($statement);
    
    /**
     * Get a driver attribute.
     */
    //public function getAttribute($attribute);

    /**
     * Get the name of the adapter that initialised the driver.
     *
     * @return string The name of the adapter that initialised the driver.
     */
    //public function getAdapterName();

    /**
     * Check whether we're currently in a transaction.
     */
    //public function inTransaction();
    
    /**
     * Get the ID of the last inserted row.
     */
    //public function lastInsertId($name=NULL);
    
    /**
     * Prepare a statement object.
     */
    //public function prepare($statement, $driver_options=array());
    
    /**
     * Execute a statement object.
     */
    //public function query($statement, $args=NULL);
    
    /**
     * Quote a string.
     */
    //public function quote($string, $parameter_type=NULL);

    /**
     * Roll back all operations attempted in the last transaction.
     */
    //public function rollBack();
    
    /**
     * Set a driver attribute.
     */
    //public function setAttribute($attribute, $value);

    /**
     * Set the name of the adapter that opened the connection.
     *
     * @param string $adapter_name The name of the adapter responsible for the
     *                             driver object's initialisation. This is
     *                             necessary for the construction of platform
     *                             objects from within the adapter manager.
     *
     * @return void
     */
    //public function setAdapterName($adapter_name);
}
