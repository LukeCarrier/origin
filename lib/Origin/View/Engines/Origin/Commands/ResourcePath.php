<?php

/**
 * Origin view engine URL command handler.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin\Commands;
use Origin\View\Engines\Origin\ICommand,
    Origin\View\Engines\Origin\Nodes\ResourcePath as ResourcePathNode;

/**
 * Resource path command.
 *
 * Provides the path to a resource within the application based on routing data
 * within the Router instance.
 */
class ResourcePath implements ICommand {
    /**
     * @override ICommand
     */
    public static function getNode($origin_template, $parser, $contents) {
        return new ResourcePathNode($origin_template, explode('.', $contents[1], 2));
    }
}
