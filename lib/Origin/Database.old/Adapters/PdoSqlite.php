<?php

/**
 * DBAL PDO SQLite adapter.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\adapters;
use Origin\Database\Dbal\IAdapter,
    Origin\Database\Dbal\Drivers\PdoSqlite as PdoSqliteDriver,
    Origin\Database\Dbal\Platforms\Sqlite as SqlitePlatform;

/**
 * SQLite adapter for DBAL.
 */
class PdoSqlite implements IAdapter {
    /**
     * @override
     */
    public function connect($uri) {
        return new PdoSqliteDriver($uri);
    }

    /**
     * @override
     */
    public function getName() {
        return 'pdosqlite';
    }

    /**
     * @override
     */
    public function getPlatform($driver) {
        return new SqlitePlatform($driver);
    }
}
