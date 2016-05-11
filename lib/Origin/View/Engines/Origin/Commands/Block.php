<?php

/**
 * Origin view engine block command handler.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin\Commands;
use Origin\View\Engines\Origin\ICommand,
    Origin\View\Engines\Origin\Nodes\BlockNode;

/**
 * Block command.
 *
 * The block command is designed to be used in conjunction with the extend and
 * yield commands. Together, these three commands enable you to create base
 * templates, or layouts, which yield specific block regions, then extend these
 * layouts from your views, replacing the yielded sections with the named blocks
 * specified in your views.
 */
class Block implements ICommand {
    /**
     * @override ICommand
     */
    public static function getNode($origin_template, $parser, $contents) {
        $parser->contextStackPush('block', $contents[1]);
        $nodes = $parser->parse(array('endblock'));
        $parser->tokenShift();
        $parser->contextStackPop('block');

        if (!$block = $parser->getObject("block.{$contents[1]}")) {
            $block = new Block($origin_template, $nodes);
            $parser->setObject("block.{$contents[1]}", $nodes);
        } else {
            $block->setNodes($nodes);
        }
    }
}
