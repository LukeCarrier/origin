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

namespace Origin\View\Engines\Origin;
use Origin\Util\StringUtil;
use Origin\View\Engines\Origin\Tokens\Block;
use Origin\View\Engines\Origin\Tokens\Text;
use Origin\View\Engines\Origin\Tokens\Variable;

class Lexer {
    const BLOCK_OPEN = '{%';
    const BLOCK_CLOSE = '%}';
    const VARIABLE_OPEN = '{{';
    const VARIABLE_CLOSE = '}}';

    private function __construct() {}

    public static function tokenise($origin_template, $raw_content) {
        $match_regex = '/('
                     .     static::BLOCK_OPEN    . '.*' . static::BLOCK_CLOSE
                     .     '|'
                     .     static::VARIABLE_OPEN . '.*' . static::VARIABLE_CLOSE
                     . ')/U';
        $chunks = preg_split($match_regex, $raw_content, NULL,
                             PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $tokens = array();

        foreach ($chunks as $chunk) {
            if ($chunk_contents = StringUtil::surroundedBy($chunk, static::BLOCK_OPEN,
                                                           static::BLOCK_CLOSE)) {
                $tokens[] = new Block($origin_template, $chunk_contents);
            } elseif ($chunk_contents = StringUtil::surroundedBy($chunk, static::VARIABLE_OPEN,
                                                                         static::VARIABLE_CLOSE)) {
                $tokens[] = new Variable($origin_template, $chunk_contents);
            } else {
                $tokens[] = new Text($origin_template, $chunk);
            }
        }

        return $tokens;
    }
}
