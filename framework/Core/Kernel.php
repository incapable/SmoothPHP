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
use SmoothPHP\Framework\Templates\TemplateEngine;

class Kernel {
    private $routeDatabase;
    private $templateEngine;
    
    public function __construct() {
        $this->routeDatabase = new RouteDatabase();
        $this->templateEngine = new TemplateEngine();
    }
    
    public function loadPrototype(WebPrototype $prototype) {
        $prototype->registerRoutes($this->routeDatabase);
    }
    
    /**
     * @return TemplateEngine
     */
    public function getTemplateEngine() {
        return $this->templateEngine;
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