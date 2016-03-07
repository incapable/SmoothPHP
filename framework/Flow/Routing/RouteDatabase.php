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

use SmoothPHP\Framework\Flow\Request\Request;

class RouteDatabase {
    const HANDLER = '-EOP-';
    private $defaults;
    
    private $routes;
    private $resolveCache;
    
    public function __construct() {
        $this->routes = array();
        $this->resolveCache = array();
        
        $this->defaults = array(
            'method' => 'GET',
            'subdomain' => '*'
        );
    }
    
    public function register(array $routeOptions) {
        $routeOpts = array_merge($this->defaults, $routeOptions);
        
        $path = explode('/', $routeOptions['path']);
        $path = array_merge(array($routeOpts['method'], $routeOpts['subdomain']), $path);
        $path = array_filter($path, 'strlen');
        
        $currentRecursive = &$this->resolveCache;
        foreach($path as $pathPart) {
            if (!isset($currentRecursive[$pathPart]))
                $currentRecursive[$pathPart] = array();
            
            $currentRecursive = &$currentRecursive[$pathPart];
        }
        
        $currentRecursive[self::HANDLER] = &$routeOpts;
        $this->routes[$routeOpts['name']] = &$routeOpts;
    }
    
    public function resolve(Request $request) {
        $base = array($request->server->REQUEST_METHOD, $request->server->SERVER_NAME);
        $resolv = array_merge($base, $request->server->REQUEST_URI);
        $resolv = array_filter($resolv, 'strlen');
        
        
    }
    
}