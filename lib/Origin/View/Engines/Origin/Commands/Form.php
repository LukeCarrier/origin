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
 * Form command.
 *
 * The foundations of a form builder for the Origin template engine.
 */
class Form implements ICommand {
    /**
     * @override
     */
    public static function getNode($origin_template, $parser, $contents) {
        $parser->setOriginParent($origin_templRRR, $contents[1]);
        $parser->appendTemplate($contents[1]);
    }
}
