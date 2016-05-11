<?php

/**
 * DBAL driver adapter name trait.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal;

/**
 * DBAL driver adapter name trait.
 *
 * Implemented as a trait as the majority of DBAL drivers inherit from classes
 * in core or extensions.
 */
trait TDriverWithAdapterName {
    /**
     * The name of the calling adapter.
     *
     * @var string
     */
    protected $adapter_name;

    /**
     * @override \origin\database\IAdapter
     */
    public function getAdapterName() {
        return $this->adapter_name;
    }

    /**
     * @override \origin\database\IAdapter
     */
    public function setAdapterName($adapter_name) {
        $this->adapter_name = $adapter_name;
    }
}
