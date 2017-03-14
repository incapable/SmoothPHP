<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * RouteDatabase.php
 * Class responsible for parsing new routes, indexing them and then decoding requests.
 */

namespace SmoothPHP\Framework\Flow\Routing;

use SmoothPHP\Framework\Core\Abstracts\Controller;
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Flow\Responses\PlainTextResponse;

class RouteDatabase {
    const HANDLER = '-EOP-';
    const WILDCARD_INPUT = '%';
    const VARARGS_INPUT = '...';

    private $defaults;
    private $routes;
    private $resolveCache;

    public function __construct() {
        $this->routes = array();
        $this->resolveCache = array();

        $this->defaults = array(
            'method' => 'GET',
            'domain' => self::WILDCARD_INPUT,
            'content-type' => PlainTextResponse::class
        );
    }

    public function register(array $routeOptions) {
        $routeOpts = array_merge($this->defaults, $routeOptions);

        if (isset($routeOpts['controllercall']))
            throw new \RuntimeException('You can not explicitly declare the controllercall route option.');

        foreach(((array) $routeOpts['method']) as $method) {
            $path = explode('/', $routeOptions['path']);
            $path = array_merge(array(strtoupper($method), $routeOpts['domain']), $path);
            $path = array_values(array_filter($path, 'strlen'));

            $currentRecursive = &$this->resolveCache;
            for ($i = 0; $i < count($path); $i++) {
                $pathPart = $path[$i];

                if ($pathPart == self::VARARGS_INPUT && $i < (count($path) - 1))
                    throw new \LogicException("Route varargs can only be used at the end of the path");

                if (!isset($currentRecursive[$pathPart]))
                    $currentRecursive[$pathPart] = array();

                $currentRecursive = &$currentRecursive[$pathPart];
            }

            $currentRecursive[self::HANDLER] = &$routeOpts;
        }

        $this->routes[$routeOpts['name']] = &$routeOpts;
    }

    public function initializeControllers(Kernel $kernel) {
        $controllers = array();
        foreach($this->routes as &$route) {
            if (!isset($controllers[$route['controller']])) {
                $controllers[$route['controller']] = new $route['controller']();
                $controllers[$route['controller']]->onInitialize($kernel);
            }
            $route['controllercall'] = new ControllerCall($controllers[$route['controller']], $route['call']);
        }
    }

    /**
     * @param $request Request $request->server->REQUEST_URI and $request->server->REQUEST_METHOD are used to determine the route.
     * @return \SmoothPHP\Framework\Flow\Routing\ResolvedRoute|bool
     */
    public function resolve(Request $request) {
        // Clean the URL of extra arguments
        $cleanedQuery = explode('?', $request->server->REQUEST_URI)[0];
        $cleanedQuery = explode('#', $cleanedQuery)[0];

        // Add the base data and split the request
        $base = array($request->server->REQUEST_METHOD, $request->server->SERVER_NAME);
        $resolveQuery = array_merge($base, array_filter(explode('/', $cleanedQuery), 'strlen'));

        // Find the route options needed for this url
        $parameters = array();
        $routeOpts = $this->findRoute(0, $this->resolveCache, $resolveQuery, $parameters);

        if (!is_array($routeOpts)) {
            // 404
            return false;
        }

        $request->meta->route = $routeOpts;
        return new ResolvedRoute($routeOpts, $parameters);
    }

    private function findRoute($depth, array &$cacheLevel, array &$query, array &$parameters) {
        if ($depth === count($query)) { // Are we at the end of the query?
            if (isset($cacheLevel[self::HANDLER]))
                return $cacheLevel[self::HANDLER]; // Notify that we found the handler
            else
                return false; // Go back 1 up in the stack, maybe we can resolve the query otherwise
        } else {
            $part = $query[$depth];

            if (isset($cacheLevel[$part])) {
                $result = $this->findRoute($depth + 1, $cacheLevel[$part], $query, $parameters);

                if (is_array($result))
                    return $result;
            }

            if (isset($cacheLevel[self::WILDCARD_INPUT])) {
                if ($depth >= 2)
                    array_push($parameters, $part); // Add it to the params list
                $result = $this->findRoute($depth + 1, $cacheLevel[self::WILDCARD_INPUT], $query, $parameters);

                if (is_array($result))
                    return $result;
                else if ($depth >= 2)
                    array_pop($parameters); // Nope that was not it
            }

            if (isset($cacheLevel[self::VARARGS_INPUT]) && isset($cacheLevel[self::VARARGS_INPUT][self::HANDLER])) {
                array_push($parameters, array_slice($query, $depth));
                return $cacheLevel[self::VARARGS_INPUT][self::HANDLER];
            }

            return false;
        }
    }

    /**
     * @param string $name
     * @return array|bool Route info
     */
    public function getRoute($name) {
        if (isset($this->routes[$name]))
            return $this->routes[$name];
        else
            return false;
    }

    /**
     * @param $routeName
     * @return Controller
     */
    public function getController($routeName) {
        return $this->routes[$routeName]['controllercall']->getController();
    }

    public function buildPath() {
        if (func_num_args() < 1)
            throw new \Exception('RouteDatabase#buildPath(...) called with no arguments, requires at least 1.');

        $route = $this->getRoute(func_get_arg(0));
        if (!$route)
            throw new \Exception(sprintf('Route \'%s\' does not exist.', func_get_arg(0)));

        $args = array_slice(func_get_args(), 1);
        $path = $route['path'];
        for($i = 0; $i < count($args); $i++) {
            $path = preg_replace('/' . self::WILDCARD_INPUT . '/', $args[$i], $path, 1, $count);
            if ($count == 1)
                continue;
            else {
                if (strpos($path, self::VARARGS_INPUT) !== false) {
                    $varArgs = implode('/', array_splice($args, $i));
                    $path = preg_replace('/' . self::VARARGS_INPUT . '/', $varArgs, $path);
                    break;
                } else
                    throw new \Exception('Not enough arguments given, route \'%s\' requested argument %d, %d given.', func_get_arg(0), $i, count($args));
            }
        }

        if (strpos($path, self::WILDCARD_INPUT) !== false || strpos($path, self::VARARGS_INPUT) !== false)
            throw new \Exception('Not enough arguments given to path.');

        return $path;
    }

    public function buildFullPath() {
        $path = call_user_func_array(array($this, 'buildPath'), func_get_args());
        $route = $this->getRoute(func_get_arg(0));

        $protocol = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? 'https' : 'http';
        $host = $route['domain'] != self::WILDCARD_INPUT ? $route['domain'] : $_SERVER['HTTP_HOST'];

        return sprintf('%s://%s%s', $protocol, $host, $path);
    }

}