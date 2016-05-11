<?php

/**
 * Origin templating engine command interface.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;

/**
 * View command interface.
 *
 * Origin view commands are used by block nodes to execute PHP code on sections
 * of the template in order to perform transformations on the template's
 * contents.
 */
interface ICommand {
	/**
	 * Transform raw template contents into a node.
	 *
	 * @param string $origin_template The fully-qualified name of the template,
	 *		  as would be specified in the controller.
	 * @param \Origin\View\Engines\Origin\Parser $parser The parser instance
	 *        that called on the command class. This is supplied to allow the
	 *        potential for altering parser state. See the parser class for more
	 *        specific details on what can be achieved through actions like
	 *        this.
	 * @param string $contents The raw contents of the template.
	 */
    //public static function getNode($origin_template, $parser, $contents);
}
