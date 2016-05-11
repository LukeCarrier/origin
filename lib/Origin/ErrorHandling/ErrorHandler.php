<?php

/**
 * Dependency injection library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\ErrorHandling;

class ErrorHandler {
    public function enable() {
        set_error_handler();
        set_exception_handler();
    }

    public function disable() {}

    public function handleError() {
        // @todo figure out what our params are
        var_dump(func_get_args());
    }

    public function handleException($exception) {}
}
