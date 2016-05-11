<?php

/**
 * Origin templating engine node array.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin;
use ArrayObject;

class NodeArray extends ArrayObject {
    public function render($file=NULL) {
        if ($file === NULL) {
            $file = new PhpFile();
        }

        foreach ($this as $node) {
            $node->render($file);
        }

        return $file->getPhp();
    }
}
