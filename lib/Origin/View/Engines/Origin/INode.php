<?php

/**
 * Origin view engine node interface.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;

/**
 * A "chunk" of a template that may be compiled into PHP source code.
 */
interface INode {
    public function __construct($origin_template, $contents);
    public function render($file);
}
