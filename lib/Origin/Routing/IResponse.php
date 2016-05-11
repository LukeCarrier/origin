<?php

/**
 * Request response library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Routing;

interface IResponse {
    public function toHttpResponse();
}
