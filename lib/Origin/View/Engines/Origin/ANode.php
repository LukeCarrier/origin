<?php

/**
 * Origin view engine node interface.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;

/**
 * Template node.
 *
 * A node is a logical "chunk" of a template from which a node may be generated.
 */
abstract class ANode {
    /**
     * The template the node was sourced from.
     *
     * @var string
     */
    protected $origin_template;

    /**
     * Default initialiser.
     *
     * All nodes should call this method before continuing with their own
     * initialisation routines, in order to ensure that the origin template of
     * the node is available for the parser.
     *
     * @param string $origin_template The qualified name of the template.
     * @param string $contents        The raw contents of the node.
     */
    public function __construct($origin_template, $contents) {
        $this->origin_template = $origin_template;
    }

    abstract public function render($file);

    /**
     * Get the name of the origin template.
     *
     * @return string The template's qualified name.
     */
    final public function getOriginTemplate() {
        return $this->origin_template;
    }
}
