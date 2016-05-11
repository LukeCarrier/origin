<?php

/**
 * Origin templating engine text token.
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
 * Text token.
 *
 * Represents a block of syntactically meaningless text within the template's
 * contents.
 */
class Text extends AToken implements IToken {
	/**
	 * Token type.
	 *
	 * @var string
	 */
    const TOKEN_TYPE = 'Text';

    /**
     * Token contents.
     *
     * @var string
     */
    protected $contents;

    /**
     * @override IToken
     */
    public function __construct($origin_template, $contents) {
        parent::__construct($origin_template, $contents);

        $this->contents = $contents;
    }

    /**
     * @override IToken
     */
    public function getContents() {
        return $this->contents;
    }
}
