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

namespace Origin\View\Engines\Null;
use Origin\View\IEngine,
    Origin\View\View;

/**
 * Null view engine.
 *
 * The null view engine performs no transformations on the content which passes
 * through it. Its primary usage is to handle the ".html" file extension for
 * templates.
 */
class Engine implements IEngine {
	/**
	 * @override IEngine
	 */
    function toPhp($view, $qualified_name, $raw_content) {
        return $raw_content;
    }
}
