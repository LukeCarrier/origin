<?php

/**
 * Symbol autoloader.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Autoload;

/**
 * Autoload namespace.
 *
 * Autoload namespaces perform the actual loading of classes on behalf of the autoloader. An autoloader will retain an
 * instance of this class to represent each registered namespace.
 */
class AutoloadNamespace {
    /**
     * The filename formatter function.
     *
     * @var callable
     */
    protected $formatter;

    /**
     * The root namespace.
     *
     * @var string
     */
    protected $root_namespace;

    /**
     * The root directory from which to attempt to source files.
     *
     * @var string
     */
    protected $root_directory;

    /**
     * Initialiser.
     *
     * @param string $root_namespace The top of the namespace for which we're responsible.
     * @param string $root_directory The directory from which to source files containing classes from.
     * @param string $formatter      The formatter to perform the filename translation.
     */
    public function __construct($root_namespace, $root_directory, callable $formatter) {
        $this->root_namespace = $root_namespace;
        $this->root_directory = $root_directory;
        $this->formatter      = $formatter;
    }

    /**
     * Get the filename formatter function.
     *
     * @return callable
     */
    public function getFormatter() {
        return $this->formatter;
    }

    /**
     * Get the root namespace.
     *
     * @return string
     */
    public function getRootNamespace() {
        return $this->root_namespace;
    }

    /**
     * Get the root directory from which to attempt to source files.
     *
     * @return string
     */
    public function getRootDirectory() {
        return $this->root_directory;
    }

    /**
     * Attempt to source the containing file for a named symbol.
     *
     * @param string $symbol The symbol whose containing file we're attempting source.
     *
     * @return boolean Whether or not the file was successfully included.
     */
    public function load($symbol) {
        $unprefixed_symbol = substr($symbol, strlen($this->root_namespace) + 1);
        $path              = $this->root_directory . '/' . call_user_func($this->formatter, $unprefixed_symbol);

        return (bool) include $path;
    }
}
