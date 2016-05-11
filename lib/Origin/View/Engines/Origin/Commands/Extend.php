<?php

/**
 * Origin view engine extend command handler.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin\Commands;
use Origin\View\Engines\Origin\ICommand;

/**
 * Extend command node.
 *
 * The extend command is designed to be used in conjunction with the block and
 * yield commands. Together, these three commands enable you to create base
 * templates, or layouts, which yield specific block regions, then extend these
 * layouts from your views, replacing the yielded sections with the named blocks
 * specified in your views.
 */
class Extend implements ICommand {
	/**
	 * @override
	 */
    public static function getNode($origin_template, $parser, $contents) {
        $parser->setOriginParent($origin_template, $contents[1]);
        $parser->appendTemplate($contents[1]);
    }
}
