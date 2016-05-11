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
    Origin\View\Engines\Origin\PhpFile;

/**
 * Text node.
 *
 * Represents a text fragment which can be ignored by the view system.
 */
class Text extends ANode implements INode {
    protected $contents;

    /**
     * @override INode
     */
    public function __construct($origin_template, $contents) {
        parent::__construct($origin_template, $contents);

        $this->contents = $contents;
    }

    /**
     * @override INode
     */
    public function render($file) {
        $file->addStatement('echo ' . var_export($this->contents, true));
    }
}
