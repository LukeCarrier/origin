<?php

/**
 * Request router library.
 *
 * Origin.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license Proprietary; all rights reserved
 */

namespace Origin\Routing;
use Origin\Http\Request;

/**
 * Request router route.
 *
 * The route class provides a cleaner interface to the request router class.
 */
class Route {
    /**
     * The namespace to prefix controllers with.
     *
     * @var string
     */
    protected static $controller_namespace;

    /**
     * The router to add generated routes to.
     *
     * @var Router
     */
    protected static $router;

    /**
     * Set the namespace to prefix controllers with.
     *
     * @param string $namespace The new namespace.
     *
     * @return void
     */
    public static function setControllerNamespace($namespace) {
        static::$controller_namespace = $namespace;
    }

    /**
     * Set the router to add generated routes to.
     *
     * @param Router $router The new router.
     *
     * @return void
     */
    public static function setRouter($router) {
        static::$router = $router;
    }

    /**
     * Generate and add a new GET route.
     *
     * @param string $path The path within the application to respond to.
     * @param callable $handler The callable that should be executed when the
     *        route matches the incoming request.
     *
     * @return void
     */
    public static function get($path, $handler) {
        $handler = static::prefixHandler(Router::translateHandler($handler));
        static::$router->addRoute($path, Request::METHOD_GET, $handler);
    }

    /**
     * Reset the router for reuse.
     *
     * @return void
     */
    public static function reset() {
        static::$controller_namespace = null;
        static::$router               = null;
    }

    /**
     * Add a set of RESTful routes for a named resource.
     *
     * @param string   $resource   The name of the resource as seen in the URL
     *                             (the slug).
     * @param string   $controller The name of the controller class that should
     *                             be instantiated to respond to the routes.
     * @param string[] $routes     A string-indexed array of action name
     *
     * @return void
     */
    public static function resource($resource, $controller, $routes=NULL) {
        static::$router->addResource($resource, static::prefixHandler($controller), $routes);
    }

    /**
     * Prefix a controller class within a handler.
     *
     * @param string|string[] $handler A class name in the form of a string or a
     *                                 handler name in the form of a two-value
     *                                 array (controller name, action name).
     *
     * @return string|string[] Either the class name or the (class name, action
     *                         name) pair, depending on the format supplied.
     */
    public static function prefixHandler($handler) {
        if (is_array($handler)) {
            if (substr($handler[0], 0, 1) !== '\\') {
                $handler[0] = static::$controller_namespace . '\\' . $handler[0];
            }
        } else {
            $handler = static::$controller_namespace . '\\' . $handler;
        }

        return $handler;
    }
}
