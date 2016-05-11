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
interface IToken {
    public function __construct($origin_template, $contents);
    public function getContents();
}
