<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * RouteDatabase.php
 * Class responsible for parsing new routes, indexing them and then decoding requests.
 */

namespace SmoothPHP\Framework\Flow\Routing;

use SmoothPHP\Framework\Flow\Requests\Request;

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
            'subdomain' => self::WILDCARD_INPUT
        );
    }
    
    public function register(array $routeOptions) {
        $routeOpts = array_merge($this->defaults, $routeOptions);
        
        $path = explode('/', $routeOptions['path']);
        $path = array_merge(array($routeOpts['method'], $routeOpts['subdomain']), $path);
        $path = array_values(array_filter($path, 'strlen'));
        
        $currentRecursive = &$this->resolveCache;
        for($i = 0; $i < count($path); $i++) {
            $pathPart = $path[$i];

            if ($pathPart == self::VARARGS_INPUT && $i < (count($path) - 1))
                throw new \LogicException("Route varargs can only be used at the end of the path");

            if (!isset($currentRecursive[$pathPart]))
                $currentRecursive[$pathPart] = array();

            $currentRecursive = &$currentRecursive[$pathPart];
        }
        
        $currentRecursive[self::HANDLER] = &$routeOpts;
        $this->routes[$routeOpts['name']] = &$routeOpts;
        
        new ControllerCall($routeOpts['controller'], $routeOpts['call']);
    }
    
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
        
        var_dump(new ResolvedRoute($routeOpts, $parameters));
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
                if ($depth > 2)
                    array_push($parameters, $part); // Add it to the params list
                $result = $this->findRoute($depth + 1, $cacheLevel[self::WILDCARD_INPUT], $query, $parameters);

                if (is_array($result))
                    return $result;
                else if ($depth > 2)
                    array_pop($parameters); // Nope that was not it
            }

            if (isset($cacheLevel[self::VARARGS_INPUT]) && isset($cacheLevel[self::VARARGS_INPUT][self::HANDLER])) {
                array_push($parameters, array_slice($query, $depth));
                return $cacheLevel[self::VARARGS_INPUT][self::HANDLER];
            }

            return false;
        }
    }
    
}