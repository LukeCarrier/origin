<?php

/**
 * Origin view engine loader library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View;

interface ILoader {
    /**
     * Load a template by its qualified name.
     *
     * @param string $qualified_name The qualified (namespaced) name of the view.
     *
     * @return string The raw file contents.
     */
    public function load($qualified_name);
}
