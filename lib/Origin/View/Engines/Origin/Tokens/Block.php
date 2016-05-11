<?php

/**
 * Origin templating engine block token.
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

/**
 * Block token.
 */
class Block extends AToken implements IToken {
    const TOKEN_TYPE = 'Block';

    protected $command = '';
    protected $arguments = '';
    protected $origin_template;

    /**
     * @override IToken
     */
    public function __construct($origin_template, $contents) {
        parent::__construct($origin_template, $contents);

        $parts = explode(' ', trim(rtrim($contents)), 2);

        $this->command   = array_shift($parts);
        $this->arguments = array_shift($parts);
    }

    /**
     * @override IToken
     */
    public function getContents() {
        return array(
            $this->command,
            $this->arguments,
        );
    }
}
