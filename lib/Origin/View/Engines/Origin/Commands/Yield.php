<?php

/**
 * Origin view engine yield command handler.
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
 * Yield command.
 *
 * The yield command is used to source a block, defined with the Block command,
 * within another template file.
 */
class Yield implements ICommand {
    /**
     * @override ICommand
     */
    public static function getNode($origin_template, $parser, $contents) {
        $block = $parser->getObject("block.{$contents[1]}");

        return clone $block;
    }
}
