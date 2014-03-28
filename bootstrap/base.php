<?php

/**
 * Framework bootstrapper.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

use Origin\Autoload\Autoloader;

$origin_root = dirname(__DIR__);

require_once "{$origin_root}/vendor/autoload.php";
require_once "{$origin_root}/lib/Origin/Autoload/Autoloader.php";

$autoloader = (new Autoloader())
           ->addNamespace('Origin', "{$origin_root}/lib/Origin")
           ->enable();

unset($origin_root);
