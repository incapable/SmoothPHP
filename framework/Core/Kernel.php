<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Kernel.php
 * Central singleton which maintains the references to all subcomponents.
 */

namespace SmoothPHP\Framework\Core;

use SmoothPHP\Framework\Flow\Routing\RouteDatabase;
use SmoothPHP\Framework\Flow\Requests\Request;

class Kernel {
    private $routeDatabase;
    
    public function __construct() {
        $this->routeDatabase = new RouteDatabase();
    }
    
    public function loadPrototype(WebPrototype $prototype) {
        $prototype->registerRoutes($this->routeDatabase);
    }
    
    /**
     * @return \SmoothPHP\Framework\Flow\Responses\Response|boolean
     */
    public function getResponse(Request $request) {
        $resolvedRoute = $this->routeDatabase->resolve($request);
        if (!$resolvedRoute)
            return false;
        return $resolvedRoute->buildResponse($this, $request);
    }
    
}