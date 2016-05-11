<?php

/**
 * Base filter library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Routing\Controllers;

/**
 * Base filter.
 *
 * Filters are classes which execute before or after the handling of the
 * request.
 */
abstract class AFilter {
    abstract public function execute($controller, $request, $response=NULL);
}
