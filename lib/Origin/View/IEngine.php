<?php

/**
 * View loader library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View;

/**
 * View engine interface.
 *
 * View engines handle the processing of raw templte files into the native PHP
 * source code ready for execution to generate the view.
 */
interface IEngine {
    /**
     * Translate a given view's raw content into PHP source code.
     *
     * @param \Origin\View\View $view The view object responsible for the
     *        compilation process.
     * @param string $qualified_name The qualified name of the view template, as
     *        would be supplied by the calling controller.
     * @param string $raw_content The raw content of the template to perform the
     *        compilation operation upon.
     * @return string Raw PHP source code, possibly containing tags for further
     *         translation by co-existing loaders.
     */
    public function toPhp($view, $qualified_name, $raw_content);
}
