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

if (!class_exists('Origin\AutoloadNamespace')) {
    require __DIR__ . '/AutoloadNamespace.php';
}

/**
 * Symbol autoloader.
 *
 * Origin's codebase is divided into namespaces, each of which contains a subset of the framework's functionality,
 * implemented as an independent, reusable component. In order to ease the loading of classes from each component,
 * Origin provides an autoloader which should be configured and registered with SPL during your application's
 * initialisation.
 *
 * In order to do this, an automatic configuration file is included with the distribution; you need only include it from
 * your initialiser like so:
 *
 *     require_once '/path/to/origin/lib/Origin.php';
 *
 * For more advanced configurations (e.g. where you need to skip initialisation Origin's dependencies and handle the
 * process yourself), you can initialise it manually. The following is the minimum configuration that will automatically
 * source the core Origin classes (where the ORIGIN_DIR constant is used purely to avoid repetition; it is not required
 * for Origin to function):
 *
 *     define('ORIGIN_DIR', '/path/to/origin');
 *     require_once ORIGIN_DIR . '/lib/autoloader/Autoloader.php';
 *     use Origin\Autoload\Autoloader;
 *     $autoloader = (new Autoloader())
 *                ->addNamespace('origin', ORIGIN_DIR . '/lib')
 *                ->enable();
 *
 * Optionally, you can have Origin's autoloader source your application's code, too. All you need to do is inform the
 * autoloader of your root namespace and the path to your code:
 *
 *     $autoloader->addNamespace('myapp', __DIR__);
 *
 * If your code doesn't follow the same layout/naming pattern as Origin's, the autoloader can still accomodate it! Just
 * supply a callable as the third parameter to addNamespace() which accepts a single string parameter (the
 * fully-qualified class name) and returns the path to the file the autoloader should attempt to include:
 *
 *     $autoloader->addNamespace('myapp', __DIR__, function($unprefixed_symbol) {
 *         return "{$unprefixed_symbol}.class.php";
 *     });
 *
 * Error handling logic is avoided within this library, as SPL takes care of it for us. This is a required architectural
 * decision to maintain interopability with other vendors' class libraries.
 */
class Autoloader {
    /**
     * The default format callable.
     *
     * Within the autoloader, formatter callables are used to translate a namespaced symbol name into a filename. This
     * string is used to identify the default formatter to use in the event that none is specified in a call to
     * addNamespace().
     *
     * @var callable
     */
    protected static $FORMAT_CALLABLE = ['\Origin\Autoload\Autoloader', 'format'];

    /**
     * Autoload namespace load callable.
     *
     * This value is used by the autoloader's error handling logic to identify file include failures whose warnings
     * should be intercepted and silenced.
     *
     * @var callable
     */
    protected static $NAMESPACE_LOAD_CALLABLE = ['\Origin\Autoload\AutoloadNamespace', 'load'];

    /**
     * Namespace separator.
     *
     * @var string
     */
    const NAMESPACE_SEPARATOR = '\\';

    /**
     * Path separator.
     *
     * @var string
     */
    const PATH_SEPARATOR = '/';

    /**
     * Is the autoloader enabled?
     *
     * @var boolean
     */
    protected $enabled;

    /**
     * The last recorded error handler.
     *
     * Populated with the previous error handler's callable when the error handler is replaced during the symbol
     * autoloading process.
     *
     * @var callable
     */
    protected $last_error_handler;

    /**
     * Registered namespaces.
     *
     * An array of the namespaces the autoloader attempts to autoload for. Each item is itself a key => array of values
     * pair, where the key is the namespace and the value is an array containing autoloader namespace objects. These
     * objects, in turn, contain the namespace metadata.
     *
     * @var \Origin\Autoload\AutoloadNamespace[][]
     */
    protected $namespaces;

    /**
     * Whether or not to override the error handler to intercept warnings emitted by include during autoload attempts.
     *
     * @var boolean
     */
    protected $override_error_handler;

    /**
     * Initialiser.
     *
     * Sets up default values for the autoloader's properties.
     *
     * @param boolean $override_error_handler During attempts to source containing files for classes, the autoloader
     *                                        will intercept errors raised by include. This is necessary as no attempt
     *                                        is made to assert that the file exists -- doing so would cause disk cache
     *                                        misses.
     */
    public function __construct($override_error_handler=true) {
        $this->enabled    = false;
        $this->namespaces = [];

        $this->override_error_handler = $override_error_handler;
    }

    /**
     * Add a namespace to the autoloader.
     *
     * In order for the autoloader to be able to locate files, the underlying file and directory names must follow some
     * form of convention whereby the path to the file containing a symbol is mapped to that of the namespace and name
     * of the symbol itself.
     *
     * In order to account for different naming schemes, it's possible to override the default filename formatter with
     * one of your own; just pass a callable value as the 3rd parameter to this method.
     *
     * @param string   $root_namespace The root namespace to add to the autoloader.
     * @param string   $root_directory The directory to associate with the specified namespace.
     * @param callable $formatter      The formatter callable, which is used to translate the symbol name into a
     *                                 filename.
     *
     * @return \Origin\Autoload\Autoloader The autoloader instance, so as to facilitate method chaining.
     */
    public function addNamespace($root_namespace, $root_directory, callable $formatter=null) {
        if ($formatter === null) {
            $formatter = static::$FORMAT_CALLABLE;
        }

        if (!array_key_exists($root_namespace, $this->namespaces)) {
            $this->namespaces[$root_namespace] = [];
        }
        $this->namespaces[$root_namespace][] = new AutoloadNamespace($root_namespace, $root_directory, $formatter);

        return $this;
    }

    /**
     * Disable the autoloader.
     *
     * Instruct SPL to unregister our autoloader, then mark the autoloader as disabled. Whilst disabled, the autoloader
     * will not be called upon to source class files.
     *
     * @return void
     *
     * @see enable()
     */
    public function disable() {
        if ($this->enabled) {
            spl_autoload_unregister([$this, 'load']);
            $this->enabled = false;
        }

        return $this;
    }

    /**
     * Enable the autoloader.
     *
     * Register our autoloader implementation with SPL, then mark the autoloader as enabled.
     *
     * @return void
     *
     * @see disable()
     */
    public function enable() {
        if (!$this->enabled) {
            spl_autoload_register([$this, 'load']);
            $this->enabled = true;
        }

        return $this;
    }

    /**
     * Default symbol name formatter.
     *
     * Given the fully-qualified name of a symbol within the application, generate and return the name of the containing
     * file.
     *
     * @param string $unprefixed_symbol The fully-qualified symbol name within the scope of the root namespace.
     *
     * @return string The formatted filename ready for sourcing via load().
     */
    public static function format($unprefixed_symbol) {
        return str_replace(static::NAMESPACE_SEPARATOR, static::PATH_SEPARATOR, $unprefixed_symbol) . '.php';
    }

    /**
     * Intercept an error raised during the autoloading process for a symbol.
     *
     * Used as the error handler during attempts to source and include the file containing a symbol. This method is
     * necessary to silence warnings caused by failed include attempt without inhibiting or incurring disk accesses.
     *
     * @param integer $code    Error code.
     * @param string  $message Descriptive message.
     * @param string  $file    Name of the file the error occurred in.
     * @param integer $line    Line number within the original file.
     * @param mixed[] $context An array containing the active symbol table at the time the error occurred.
     *
     * @return boolean Always true, so as to avoid the execution of PHP's own error handler.
     */
    public function handleError($code, $message, $file, $line, $context) {
        $backtrace = debug_backtrace();
        $trigger   = $backtrace[1];

        $this->restoreErrorHandler();

        list($class, $method) = static::$NAMESPACE_LOAD_CALLABLE;

        return (array_key_exists('class', $trigger)
                && "\\{$trigger['class']}" === $class && $trigger['function'] === $method);
    }

    /**
     * Attempt to autoload a given symbol.
     *
     * This method should not be called manually; when registered as an autoloader with SPL, PHP will call it behind the
     * scenes when you attempt to use a class which has not yet been loaded.
     *
     * @param string $symbol The name of the symbol we're seeking.
     *
     * @return boolean True on a successful file inclusion, false otherwise. Note that the symbol may still not be
     *                 loaded even when this method returns true, since we only care about having loaded the file and
     *                 don't check for the presence of a symbol. 
     */
    public function load($symbol) {
        $ns_components = explode(static::NAMESPACE_SEPARATOR, $symbol);
        $sourced       = false;

        $this->setErrorHandler();

        do {
            array_pop($ns_components);
            $namespace = implode(static::NAMESPACE_SEPARATOR, $ns_components);

            if (array_key_exists($namespace, $this->namespaces)) {
                foreach ($this->namespaces[$namespace] as $autoload_namespace) {
                    $sourced = $autoload_namespace->load($symbol);
                }
            }
        } while ($sourced === false && count($ns_components) > 0);

        $this->restoreErrorHandler();

        return $sourced;
    }

    /**
     * Temporarily override the error handler.
     *
     * @return void
     */
    protected function setErrorHandler() {
        if ($this->override_error_handler) {
            $this->last_error_handler = set_error_handler([$this, 'handleError']);
        }
    }

    /**
     * Restore the overridden error handler.
     *
     * @return void
     */
    protected function restoreErrorHandler() {
        if ($this->last_error_handler !== null) {
            set_error_handler($this->last_error_handler);
        }

        $this->last_error_handler = null;
    }
}
