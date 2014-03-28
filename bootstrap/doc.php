#!/usr/bin/env php
<?php

/**
 * Docuentation generation bootstrapper.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

require_once __DIR__ . '/base.php';

if (@date_default_timezone_get()) {
    date_default_timezone_set('UTC');
}

require_once dirname(__DIR__) . '/vendor/bin/phpdoc.php';
