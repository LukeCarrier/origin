<?php

/**
 * Origin view engine translate command handler.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin\Commands;
use Origin\View\Engines\Origin\ICommand,
    Origin\View\Engines\Origin\Nodes\Translate as TranslateNode;

/**
 * Translation command.
 *
 * The translation command uses the localisation API to translate strings using
 * configured language pack sources.
 */
class Translate implements ICommand {
    /**
     * @override ICommand
     */
    public static function getNode($origin_template, $parser, $contents) {
        return new TranslateNode($origin_template, $contents[1]);
    }
}
