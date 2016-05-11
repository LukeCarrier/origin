<?php

/**
 * View library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View\Errors;

use Exception;

/**
 * No matching views exception.
 *
 * This exception should be thrown when a developer attempts to source a view which cannot be provided by the registered
 * loaders.
 */
class NoMatchingViews extends Exception {
    /**
     * Format string for the exception message.
     *
     * @var string
     */
    const MESSAGE_FORMAT = 'No such view \'%s\'';

    /**
     * Qualified name of the view.
     *
     * @var string
     */
    protected $qualified_name;

    /**
     * @override \Exception
     *
     * @param string $qualified_name Qualified name of the view.
     */
    public function __construct($qualified_name) {
        $this->qualified_name = $qualified_name;

        parent::__construct($this->getRealMessage());
    }

    /**
     * @override \Exception
     */
    public function __toString() {
        return __NAMESPACE__ . '\\' . __CLASS__ . ': ' . $this->getRealMessage();
    }

    /**
     * Get the exception's real message.
     *
     * We use the same exception message in {@link __construct()} and {@link __toString()}, so we generate it here for
     * easy reuse.
     *
     * @return string The exception message.
     */
    protected function getRealMessage() {
        return sprintf(static::MESSAGE_FORMAT, $this->qualified_name);
    }
}
