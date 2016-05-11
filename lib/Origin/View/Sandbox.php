<?php

/**
 * View loader library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\View;
use Origin\Cache\Disk\Disk,
    Origin\View\Errors\NoMatchingViews,
    Origin\View\PhpFile;

/**
 * View sandbox library.
 *
 * The view sandbox provides a safe execution harness for template code generated by view engines. As a means of
 * containment, it prevents templates from accessing variables declared out of scope.
 */
class Sandbox {
    /**
     * Rendered contents of the template.
     *
     * @var string
     */
    protected $contents = '';

    /**
     * Variables added with {@link setVariables}.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Return the string representation of the template.
     *
     * @return string The contents of the template.
     */
    public function __toString() {
        return $this->contents;
    }

    /**
     * Load a cached PHP file.
     *
     * Attempt to source a cached, pre-compiled PHP representation of a template ready for execution and execute it. If
     * we're successful, it will be possible to cast the view sandbox object to string in order to retrieve its executed
     * contents. If we fail, likely because the cache didn't have a pre-compiled template, throw an exception so that
     * the view library knows to compile the template.
     *
     * @param \Origin\Cache\Disk\Disk $cache The disk cache instance to attempt to retrieve the file from.
     * @param string                  $index The index to locate the file using, generally the "qualified name" of the
     *                                       template.
     *
     * @throws NoMatch A NoMatch exception is raised if the attempt to locate the file within the cache fails.
     */
    public function loadCachedPhpFile(Disk $cache, $index) {
        $filename = $cache->getFilename($index);

        ob_start();
        $result = @include $filename;
        $this->contents = ob_get_contents();
        ob_end_clean();

        if (!$result) {
            throw new NoMatchingViews($index);
        }
    }

    /**
     * Load raw PHP source code.
     *
     * Evaluate a string containing raw PHP source code, capturing the output to return.
     *
     * This is a considerably slower approach than sourcing the content from the cache, but may be desirable if you wish
     * to disable the cache for debugging purposes. It's also used as a fallback if we can't write the contents to the
     * cache, for instance if we're unable to obtain an exclusive write lock on the designated cache index, or if the
     * the cache directory permissions don't grant us write privileges.
     *
     * @param string $contents The stirng containing the PHP source code.
     */
    public function loadRawPhp($contents) {
        ob_start();
        eval(PhpFile::PHP_CLOSE . $contents);
        $this->contents = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Set the variables available within the template.
     *
     * @param mixed[] $variables The variables to make available within the template.
     */
    public function setVariables($variables) {
        $this->variables = $variables;
    }
}