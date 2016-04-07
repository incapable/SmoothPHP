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
use SmoothPHP\Framework\Database\MySQL;

class Kernel {
    // Site data
    private $config;
    private $routeDatabase;
    
    // Runtime
    private $templateEngine;
    private $mysql;
    
    public function __construct() {
        $this->config = new Config();
        $this->routeDatabase = new RouteDatabase();
        $this->templateEngine = new TemplateEngine();
    }
    
    public function loadPrototype(WebPrototype $prototype) {
        $prototype->initialize($this);
        $prototype->registerRoutes($this->routeDatabase);
    }
    
    /**
     * @return Config
     */
    public function &getConfig() {
        return $this->config;
    }
    
    /**
     * @return TemplateEngine
     */
    public function getTemplateEngine() {
        return $this->templateEngine;
    }
    
    /**
     * @return MySQL
     */
    public function getMySQL() {
        if (!$this->config->mysql_enabled)
            throw new \RuntimeException("MySQL is not enabled");
        if (!isset($this->mysql))
            $this->mysql = new MySQL($this->config);
        return $this->mysql;
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