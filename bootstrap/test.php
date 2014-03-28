<?php

/**
 * Test bootstrapper.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

require_once __DIR__ . '/base.php';

$autoloader->addNamespace('Origin\TestFramework', dirname(__DIR__) . '/test-lib/Origin/TestFramework')
           ->addNamespace('Origin\TestSupport',   dirname(__DIR__) . '/test-lib/Origin/TestSupport');

if (@date_default_timezone_get()) {
    date_default_timezone_set('UTC');
}

if (php_sapi_name() !== 'cli') {
    ini_set('html_errors', 'on');
}

foreach (['children', 'data', 'depth'] as $type) {
    ini_set("xdebug.var_display_max_{$type}", -1);
}
