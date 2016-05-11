<?php

/**
 * Origin view engine library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin\Nodes;
use Origin\View\Engines\Origin\ANode,
    Origin\View\Engines\Origin\INode,
    Origin\View\Engines\Origin\Errors\NoSuchCommand;

/**
 * Block node.
 *
 * Represents a block object within a view template file.
 */
class Block extends ANode implements INode {
    /**
     * Block command.
     */
    protected $command;

    /**
     * Command arguments.
     */
    protected $arguments;

    /**
     * @override INode
     */
    public function __construct($origin_template, $contents) {
        $this->origin_template = $origin_template;
        list($this->command, $this->arguments) = $contents; 
    }

    /**
     * @override INode
     */
    public function render($file) {
        $file->addContent($this->command);
    }
}
