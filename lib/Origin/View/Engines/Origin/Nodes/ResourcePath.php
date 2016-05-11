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
 * Resource path node.
 *
 * Represents a resource path object within a view template.
 */
class ResourcePath extends ANode implements INode {
    const ROUTER_VARIABLE = 'router';

    protected $action;
    protected $resource;

    /**
     * @override INode
     */
    public function __construct($origin_template, $contents) {
        parent::__construct($origin_template, $contents);

        list($this->resource, $this->action) = $contents;
    }

    /**
     * @override INode
     */
    public function render($file) {
        $router = PhpFile::getVariableReference(static::ROUTER_VARIABLE);
        $call = PhpFile::getFunctionCall($router . '->getResourcePath', array($this->resource,
                                                                             $this->action));
        $file->addStatement('echo ' . $call);
    }
}
