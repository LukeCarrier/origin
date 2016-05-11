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

namespace Origin\View;
use Origin\Cache\Disk\DiskCache,
    Origin\View\Errors\NoMatchingViews;

/**
 * View wrapper.
 *
 * The view wrapper simplifies working with the many different components of the
 * view library. It provides a simple configuration interface that binds the
 * cache, templating engines and template loaders and interfaces with the
 * sandbox on your behalf.
 *
 * The process for loading the view involves binding multiple different
 * components and is likely a task for your dependency injection library:
 *
 * @code
 * $view = new View();
 * $view->setCache(new Origin\Cache\Disk\DiskCache());
 * $view->addLoader(new Origin\View\Loaders\File());
 * 
 * $view->addEngine('html',   new Origin\View\Engines\Null\Engine());
 * $view->addEngine('origin', new Origin\View\Engines\Null\Origin()));
 * 
 * $view->setVariable('thing', $value);
 * @endcode
 *
 * Once your configuration is set, rendering the view is as simple as passing an
 * identifier supported by one of the template loaders you've added to the
 * wrapper:
 *
 * @code
 * $sandbox = $view->render('static_pages.index');
 * @endcode
 *
 * {@link render()} returns a sandbox object which has a __toString() handler
 * defined and can be treated as such.
 */
class View {
    /**
     * View cache.
     *
     * @see {@link setCache()}
     * @var Origin\Cache\Disk\DiskCache
     */
    protected $cache;

    /**
     * Loaded view engines.
     *
     * @see {@link addEngine()}
     * @var array<string, IEngine>
     */
    protected $engines = [];

    /**
     * Registered view loaders.
     *
     * @see {@link addLoader()}
     * @var array<integer, ILoader>
     */
    protected $loaders = [];

    /**
     * Default variable values.
     *
     * @var array<string, mixed>
     */
    protected $variables = [];

    /**
     * Add a view engine.
     *
     * The contents of a template can be passed through any number of template
     * engines as part of its compilation process, allowing data to be easily
     * exchanged between different engines as necessary. The order in which the
     * template contents is passed through tese engines is predictable, but
     * dependent upon the template loaders your application uses to source its
     * templates.
     *
     * @param string $name The engine's name and file extension.
     * @param string $instance The engine instance.
     */
    public function addEngine($name, $instance) {
        $this->engines[$name] = $instance;
    }

    /**
     * Add a view loader.
     *
     * View loaders are responsible for sourcing the contents of the different
     * templates required for the construction of the view. They are executed in
     * the order in which they have been added to the view wrapper.
     *
     * @param ILoader The instantiated loader.
     */
    public function addLoader(ILoader $loader) {
        $this->loaders[] = $loader;
    }

    /**
     * Get PHP from a template.
     *
     * @todo What happens if one of the file's engines has not yet been
     *       registered? We should probably throw an exception if this occurs?
     *
     * @param string $qualified_name The name of the template to source.
     * @return string The raw PHP source code generated from the template's
     *         compilation.
     */
    public function getPhp($qualified_name) {
        list($contents, $engines) = $this->getRawTemplate($qualified_name);
        while ($engine = array_shift($engines)) {
            $contents = $engine->toPhp($this, $qualified_name, $contents);
        }

        return $contents;
    }

    /**
     * Get the raw contents of a template.
     *
     * This method is used by the parser to source template files necessary to
     * compile the template.
     *
     * @param string $qualified_name The qualified name of the template.
     * @return array The template's raw contents.
     */
    public function getRawTemplate($qualified_name) {
        foreach ($this->loaders as $loader) {
            if (list($raw_contents, $engines) = $loader->load($qualified_name)) {
                $engines = array_intersect_key($this->engines, array_flip($engines));
                return array($raw_contents, $engines);
            }
        }

        throw new NoMatchingViews($qualified_name);
    }

    /**
     * Does this view instance have a cache?
     *
     * @return boolean Whether or not the view instance has a cache.
     */
    public function hasCache() {
        return !!$this->cache;
    }

    /**
     * Load a file into an existing sandbox.
     *
     * Sources a template, first attempting the cache via
     * {@link loadCachedPhpFile()}, falling back on manually fetching the
     * template's contents and inserting them into the cache.
     *
     * @param string $qualified_name The qualified name of the template to
     *        source.
     * @param Sandbox $sandbox The view sandbox to load the (possibly cached)
     *        PHP source into.
     */
    public function loadIntoSandbox($qualified_name, $sandbox) {
        if ($this->hasCache()) {
            try {
                $sandbox->loadCachedPhpFile($this->cache, $qualified_name);
            } catch (NoMatchingViews $e) {
                $contents = static::getPhp($qualified_name);
                $this->cache->put($qualified_name, $contents);

                $sandbox->loadCachedPhpFile($this->cache, $qualified_name);
            }
        } else {
            $contents = static::getPhp($qualified_name);
            $sandbox->loadRawPhp($contents);
        }
    }

    /**
     * Sandbox and execute a template to render a view.
     *
     * Shorthand wrapper for instantiating a new sandbox, passing it our
     * variables and loading the template contents into the sandbox.
     *
     * @param string $qualified_name The qualified (namespaced) name of the
     *        view; in the form "directory.basename".
     * @return Sandbox An evaluation sandbox for the view to be rendered within.
     */
    public function render($qualified_name) {
        $sandbox = new Sandbox();
        $sandbox->setVariables($this->variables);
        $this->loadIntoSandbox($qualified_name, $sandbox);

        return $sandbox;
    }

    /**
     * Set the disk cache.
     *
     * The view wrapper will attempt to source the pre-compiled template from
     * the designated cache before compiling them, thus reducing the time
     * necessary for the processing of incoming requests. Once the template has
     * been compiled to PHP, the wrapper will attempt to write the file back to
     * the cache.
     *
     * @param DiskCache|NULL $cache The disk cache, or NULL to disable caching.
     */
    public function setCache($cache) {
        $this->cache = $cache;
    }

    /**
     * Set the value of a variable.
     *
     * Since compiled template code is executed within a {@link ViewSandbox}
     * object and not within the global scope, variables from the global scope
     * are inaccessible to the template code (an intentional design choice made
     * with security in mind). Thus, to access a variable within your template,
     * you'll need to first add it to the {@link $variables} array.
     *
     * @param string $name The variable's name.
     * @param mixed $value The variable's value.
     */
    public function setVariable($name, $value) {
        $this->variables[$name] = $value;
    }
}
