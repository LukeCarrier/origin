<?php

/**
 * DBAL PDO (generic) driver.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Database\Dbal\drivers;
use PDO as PdoBase,
    Origin\Database\Dbal\IDriver;

class Pdo extends PdoBase implements IDriver {
    /**
     * The DBAL platform, as obtained from the driver.
     *
     * @var IDriver
     */
    protected $platform;

    public function __construct($uri) {
        parent::__construct($uri);
    }

    /**
     * Set connection options.
     *
     * @param array<string, string> $options The options.
     */
    public function setOptions($options) {
        foreach ($options as $option => $option_value) {
            switch ($option) {
                case 'error_mode':
                    $attribute = PDO::ATTR_ERRMODE;
                    switch ($option_value) {
                        case 'exception': $attribute_value = PDO::ERRMODE_EXCEPTION; break;
                        case 'silent':    $attribute_value = PDO::ERRMODE_SILENT;    break;
                        case 'warning':   $attribute_value = PDO::ERRMODE_WARNING;   break;
                    }
                    break;
            }

            $this->setAttribute($attribute, $attribute_value);
        }
    }
}
