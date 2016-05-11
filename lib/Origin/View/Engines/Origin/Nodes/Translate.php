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
 * Translate node.
 *
 * Represents a translate object within the view.
 */
class Translate extends ANode implements INode {
    const LOCALE_VARIABLE = 'locale';

    protected $string;

    /**
     * @override INode
     */
    public function __construct($origin_template, $contents) {
        parent::__construct($origin_template, $contents);

        $this->string = $contents;
    }

    /**
     * @override INode
     */
    public function render($file) {
        $locale = PhpFile::getVariableReference(static::LOCALE_VARIABLE);
        $call = PhpFile::getFunctionCall($locale . '->translate', $this->string);
        $file->addStatement('echo ' . $call);
    }
}
