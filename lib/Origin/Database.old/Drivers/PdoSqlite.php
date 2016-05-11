<?php

/**
 * DBAL PDO SQLite driver.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\drivers;
use Origin\Database\Dbal\IDriver,
    Origin\Database\Dbal\TDriverWithAdapterName,
    Origin\Database\Dbal\Drivers\Pdo;

/**
 * SQLite driver for DBAL.
 */
class PdoSqlite extends Pdo implements IDriver {
    use TDriverWithAdapterName;

    /**
     * @override
     */
    public function __construct($uri) {
        $parsed_url = parse_url($uri);
        $uri = 'sqlite:' . $parsed_url['path'];

        parent::__construct($uri);

        if (array_key_exists('query', $parsed_url)) {
            parse_str($parsed_url['query'], $options);
            $this->setOptions($options);
        }
    }
}
