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

namespace Origin\Database\Dbal\Errors;
use Exception;

/**
 * No such adapter exception.
 */
class NoSuchAdapter extends Exception {
    /**
     * The adapter name.
     *
     * @var string
     */
    protected $adapter_name;

    /**
     * Initialiser.
     *
     * @param string $adapter_name The name of the adapter.
     */
    public function __construct($adapter_name) {
        $this->adapter_name = $adapter_name;
    }

    /**
     * @override
     */
    public function __toString() {
        return __NAMESPACE__ . '\\' . __CLASS__
                 . ": adapter '{$this->adapter_name}' is not known";
    }

    /**
     * Get the adapter name.
     *
     * @return string The name of the adapter.
     */
    public function getAdapterName() {
        return $this->adapter_name;
    }
}
