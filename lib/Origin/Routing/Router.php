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

use Origin\Http\Request,
    Origin\Routing\Errors\InvalidPath,
    Origin\Routing\Errors\NoMatchingRoute,
    Origin\Util\StringInflexerUtil;

/**
 * Request router.
 *
 * Origin's request router is designed to dispatch requests to controllers based upon a set of routing rules. It enables
 * you to split your application's business logic into a set of classes (referred to as "controllers"), each generally
 * being responsible for all of the operations associated with a certain type of object or relationship.
 */
class Router {
    /**
     * Regular expression to match all tokens in a route's path.
     *
     * @var string
     */
    const REGEX_TOKENS = '/:([a-zA-Z]+)/';

    /**
     * Create a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_CREATE = 'createAction';

    /**
     * Delete a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_DELETE = 'deleteAction';

    /**
     * Destroy a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_DESTROY = 'destroyAction';

    /**
     * Edit a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_EDIT = 'editAction';

    /**
     * Index a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_INDEX = 'indexAction';

    /**
     * New a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_NEW = 'newAction';

    /**
     * Update a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_UPDATE = 'updateAction';

    /**
     * View a resource.
     *
     * @var string
     */
    const RESOURCE_ACTION_VIEW = 'viewAction';

    /**
     * Create a resource.
     *
     * @var string
     */
    const RESOURCE_CREATE = 'create';

    /**
     * Delete a resource.
     *
     * @var string
     */
    const RESOURCE_DELETE = 'delete';

    /**
     * Destroy a resource.
     *
     * @var string
     */
    const RESOURCE_DESTROY = 'destroy';

    /**
     * Edit a resource.
     *
     * @var string
     */
    const RESOURCE_EDIT = 'edit';

    /**
     * Index a resource.
     *
     * @var string
     */
    const RESOURCE_INDEX = 'index';

    /**
     * New a resource.
     *
     * @var string
     */
    const RESOURCE_NEW = 'new';

    /**
     * Update a resource.
     *
     * @var string
     */
    const RESOURCE_UPDATE = 'update';

    /**
     * View a resource.
     *
     * @var string
     */
    const RESOURCE_VIEW = 'view';

    /**
     * Before filter type.
     *
     * @var int
     */
    const FILTER_BEFORE = 0;

    /**
     * After filter type.
     *
     * @var int
     */
    const FILTER_AFTER = 1;

   /**
     * Default resource routes.
     *
     * @var string[][]
     * @see {@link resource()}
     */
    protected $default_resource_routes = NULL;

    /**
     * Application base URL.
     *
     * @var string
     */
    protected $base_url;

    /**
     * Controller factory.
     *
     * @var \Origin\Routing\IControllerFactory
     */
    protected $controller_factory;

    /**
     * Registered routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Registered resources.
     *
     * @var array
     */
    protected $resources = [];

    /**
     * Request filters.
     *
     * @var string[][]
     */
    protected $filters = [];

    /**
     * Initialiser.
     *
     * @param \Origin\Routing\IControllerFactory $controller_factory The controller factory to be used for controller
     *                                                               instantiation during request dispatching.
     * @param string                             $base_url           The application's base URL.
     *
     * @todo Make it possible to override the default route set.
     */
    public function __construct(IControllerFactory $controller_factory, $base_url='/') {
        $this->controller_factory = $controller_factory;
        $this->base_url           = $base_url;

        $this->filters = [
            static::FILTER_BEFORE => [],
            static::FILTER_AFTER  => [],
        ];

        $this->default_resource_routes = [
            static::RESOURCE_CREATE  => [Request::METHOD_POST,   '/{:resource}',            static::RESOURCE_ACTION_CREATE ],
            static::RESOURCE_DELETE  => [Request::METHOD_GET,    '/{:resource}/:id/delete', static::RESOURCE_ACTION_DELETE ],
            static::RESOURCE_DESTROY => [Request::METHOD_DELETE, '/{:resource}/:id',        static::RESOURCE_ACTION_DESTROY],
            static::RESOURCE_EDIT    => [Request::METHOD_GET,    '/{:resource}/:id/edit',   static::RESOURCE_ACTION_EDIT   ],
            static::RESOURCE_INDEX   => [Request::METHOD_GET,    '/{:resources}',           static::RESOURCE_ACTION_INDEX  ],
            static::RESOURCE_NEW     => [Request::METHOD_GET,    '/{:resource}/new',        static::RESOURCE_ACTION_NEW    ],
            static::RESOURCE_UPDATE  => [Request::METHOD_POST,   '/{:resource}/:id',        static::RESOURCE_ACTION_UPDATE ],
            static::RESOURCE_VIEW    => [Request::METHOD_GET,    '/{:resource}/:id',        static::RESOURCE_ACTION_VIEW   ],
        ];
    }

    /**
     * Add before filter.
     *
     * @param string $class_name The (namespaced) name of the filter class.
     *
     * @return void
     */
    public function addBeforeFilter($class_name) {
        $this->addFilter(static::FILTER_BEFORE, $class_name);
    }

    /**
     * Add after filter.
     *
     * @param string $class_name The (namespaced) name of the filter class.
     *
     * @return void
     */
    public function addAfterFilter($class_name) {
        $this->addFilter(static::FILTER_AFTER, $class_name);
    }

    /**
     * Add a filter.
     *
     * @param integer $type       One of the FILTER_* constants.
     * @param string  $class_name The namespaced name of the filter class.
     *
     * @return void
     */
    public function addFilter($type, $class_name) {
        $this->filters[$type][] = $class_name;
    }

    /**
     * Execute filters of a specific type with
     *
     * @return void
     */
    public function executeFilters($type, $controller, $request, $response=NULL) {
        foreach ($this->filters[$type] as $filter) {
            $filter->execute($controller, $request, $response);
        }
    }

    /**
     * Add a resource.
     *
     * Registers a RESTful resource for the purposes of routing and later URL
     * retrieval and generation.
     *
     * @param string   $resource   The name of the resource as seen in the URL (the slug).
     * @param string   $controller The name of the controller class that should be instantiated to respond to the
     *                             routes.
     * @param string[] $routes     A string-indexed array of action name
     *
     * @return void
     */
    public function addResource($resource, $controller, $routes=NULL) {
        $plural         = StringInflexerUtil::pluralise($resource);
        $default_routes = $this->generateDefaultRoutes($resource, $plural);

        if (is_array($routes)) {
            $routes = array_merge($default_routes, $routes);
        } else {
            $routes = &$default_routes;
        }

        foreach ($routes as $action => &$route) {
            list($method, $path, $handler_method) = $route;
            $handler = [$controller, $handler_method];

            $this->addRoute($path, $method, $handler);
        }

        $this->resources[$resource] = $routes;
    }

    /**
     * Add a route.
     *
     * @param string      $path    The path within the application to respond to.
     * @param string|null $method  An HTTP method, in lowercase. For guidance, see the \Origin\Http\Request::METHOD_*
     *                             constants.
     * @param callable    $handler The callable that should be executed when the route matches the incoming request.
     *
     * @return void
     */
    public function addRoute($path, $method, $handler) {
        $path_regex = preg_replace(static::REGEX_TOKENS, '([0-9a-zA-Z]+)', $path);
        $path_regex = "%^{$path_regex}$%";

        $this->routes[$method] = (array_key_exists($method, $this->routes)) ? $this->routes[$method] : [];
        $this->routes[$method][$path_regex] = [
            static::translateHandler($handler),
            $path,
        ];
    }

    /**
     * Generate a default set of routes for the given resource.
     *
     * This is necessary to facilitate the automatic formatting of all of the
     * slugs, which are dynamically substituted based upon the default route
     * set.
     *
     * @param string $resource        The name of the resource which will be
     *                                used as its slug in URLs.
     * @param string $resource_plural The plural version of the resource name.
     *
     * @return void
     */
    protected function generateDefaultRoutes($resource, $resource_plural) {
        $search  = ['{:resources}',   '{:resource}'];
        $replace = [$resource_plural, $resource    ];

        $result = [];
        foreach ($this->default_resource_routes as $action => $route) {
            $result[$action] = [
                $route[0],
                str_replace($search, $replace, $route[1]),
                $route[2],
            ];
        }

        return $result;
    }

    /**
     * Dispatch on a route.
     *
     * @param \Origin\Http\Request $request An Origin HTTP request object.
     */
    public function dispatch(Request $request) {
        $method = $request->getMethod();
        $path   = $request->getUrl();

        $handler = $this->route($path, $method);

        if (is_array($handler)) {
            list($controller_class, $method) = $handler;
            $controller = $this->controller_factory->get($controller_class);
            $callable = array($controller, $handler[1]);
        }

        $this->executeFilters(static::FILTER_BEFORE, $controller, $request);
        $result = call_user_func($callable);
        $response = $result->toHttpResponse();
        $this->executeFilters(static::FILTER_AFTER, $controller, $request, $response);

        $this->respondWith($response);
    }

    /**
     * Get the path to a resource.
     *
     * @todo Substitute parameters!
     */
    public function getResourcePath($resource, $action, $parameters=[]) {
        if (!array_key_exists($resource, $this->resources)
                || !array_key_exists($action, $this->resources[$resource])) {
            throw new NoMatchingRoute();
        }

        list($method, $path) = $this->resources[$resource][$action];

        return $path;
    }

    /**
     * Find the first matching route.
     *
     * @throws NoMatchingRoute Raises an exception when no matching route
     *                         can be found.
     */
    public function route($path, $method) {
        $path = substr($path, strlen($this->base_url));

        if ($path === false || strlen($path) === 0) {
            throw new InvalidPath($path);
        }
        
        foreach ($this->routes[$method] as $route_path_regex => $route) {
            list($route_handler, $route_path) = $route;

            if (preg_match($route_path_regex, $path)) {
                return $route_handler;
            }
        }

        throw new NoMatchingRoute($path, $method);
    }

    /**
     * Return an HTTP response object to the client browser.
     *
     * @param Origin\Http\Response $response The HTTP response.
     */
    public function respondWith($response) {
        foreach ($response->getHeaders() as $name => $value) {
            header('{$name}: {$value}');
        }

        echo $response->getBody();
    }

    /**
     * Translate a handler alias into a handler.
     *
     * @param callable $handler The handler, usually a string in the form
     *                          'Controller->method'.
     * @return callable The translated handler.
     */
    public static function translateHandler($handler) {
        if (is_string($handler) && strstr($handler, '->')) {
            $handler = explode('->', $handler, 2);
        }

        return $handler;
    }
}
