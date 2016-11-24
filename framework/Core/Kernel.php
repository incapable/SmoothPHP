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

use SmoothPHP\Framework\Cache\Assets\AssetsRegister;
use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Flow\Routing\RouteDatabase;
use SmoothPHP\Framework\Templates\TemplateEngine;

class Kernel {
    public static $instance;

    // Site data
    private $config;
    private $routeDatabase;

    // Runtime
    private $templateEngine;
    private $assetsRegister;
    private $mysql;

    public function __construct() {
        $this->config = new Config();
        $this->routeDatabase = new RouteDatabase();
        $this->assetsRegister = new AssetsRegister();
    }

    public function loadPrototype(WebPrototype $prototype) {
        $prototype->initialize($this);
        $prototype->registerRoutes($this->routeDatabase);
        define('__DEBUG__', $this->config->debug);
        $this->templateEngine = new TemplateEngine();
        $this->assetsRegister->initialize($this);
    }

    /**
     * @return Config
     */
    public function &getConfig() {
        return $this->config;
    }

    /**
     * @return RouteDatabase
     */
    public function getRouteDatabase() {
        return $this->routeDatabase;
    }

    /**
     * @return TemplateEngine
     */
    public function getTemplateEngine() {
        return $this->templateEngine;
    }

    /**
     * @return AssetsRegister
     */
    public function getAssetsRegister() {
        return $this->assetsRegister;
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
     * @param Request $request The request that determines how the response is made, and how it is given.
     * @return \SmoothPHP\Framework\Flow\Responses\Response|boolean
     */
    public function getResponse(Request $request) {
        $resolvedRoute = $this->routeDatabase->resolve($request);
        if (!$resolvedRoute)
            return false;
        return $resolvedRoute->buildResponse($this, $request);
    }

}