<?php

/**
 * Application logging API.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Logging;

/**
 * Abstract log target.
 */
abstract class ATarget {
    /**
     * Initialiser.
     *
     * Instantiates the specified log message formatter, falling back to the target's default formatter if the developer
     * didn't specify one.
     *
     * @param \Origin\Logging\IFormatter $formatter The formatter.
     */
    public function __construct($formatter=null) {
        $this->formatter = $formatter ?: $this->getDefaultFormatter();
    }
}
