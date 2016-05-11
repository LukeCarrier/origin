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
    Origin\View\Engines\Origin\INode;

/**
 * Variable node.
 *
 * Represents a variable reference within a view, as set by View->setVariable().
 */
class Variable extends ANode implements INode {
    protected $name;

    /**
     * @override INode
     */
    public function __construct($origin_template, $contents) {
        $this->name = $contents;
    }

    /**
     * @override INode
     */
    public function render($file) {
        $accessor = '$this->variables[' . var_export($this->name, true) . ']';
        $file->addStatement("echo {$accessor}");
    }
}
