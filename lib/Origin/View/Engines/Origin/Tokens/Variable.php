<?php

/**
 * Origin templating engine variable token.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Engines\Origin\Tokens;
use Origin\View\Engines\Origin\AToken,
    Origin\View\Engines\Origin\IToken;

class Variable extends AToken implements IToken {
    const TOKEN_TYPE = 'Variable';

    protected $name;

    /**
     * @override IToken
     */
    public function __construct($origin_template, $contents) {
        parent::__construct($origin_template, $contents);

        $this->name = trim(rtrim($contents));
    }

    /**
     * @override IToken
     */
    public function getContents() {
        return $this->name;
    }
}
