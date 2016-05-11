<?php

/**
 * Origin view engine token interface.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;

/**
 * A "chunk" of a template from which a node may be generated.
 */
abstract class AToken {
    /**
     * The template the token was sourced from.
     *
     * @var string
     */
    protected $origin_template;

    /**
     * Default initialiser.
     *
     * All tokens should call this method before continuing with their own
     * initialisation routines, in order to ensure that the origin template of
     * the token is available for the parser.
     *
     * @param string $origin_template The qualified name of the template.
     * @param string $contents The raw contents of the token.
     */
    public function __construct($origin_template, $contents) {
        $this->origin_template = $origin_template;
    }

    abstract public function getContents();

    /**
     * Get the name of the origin template.
     *
     * @return string The template's qualified name.
     */
    final public function getOriginTemplate() {
        return $this->origin_template;
    }
}
