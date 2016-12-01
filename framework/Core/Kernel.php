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
use SmoothPHP\Framework\Core\Abstracts\WebPrototype;
use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Flow\Responses\PlainTextResponse;
use SmoothPHP\Framework\Flow\Routing\RouteDatabase;
use SmoothPHP\Framework\Localization\FileDataSource;
use SmoothPHP\Framework\Localization\LanguageRepository;
use SmoothPHP\Framework\Templates\TemplateEngine;

class Kernel {
    public static $instance;

    // Site data
    private $config;
    private $routeDatabase;

    // Runtime
    private $templateEngine;
    private $assetsRegister;
    private $languagerepo;
    private $mysql;

    public function __construct() {
        $this->config = new Config();
        $this->routeDatabase = new RouteDatabase();
        $this->assetsRegister = new AssetsRegister();
        $this->languagerepo = new LanguageRepository($this);

        // Initialise the PHP session
        session_name('smsid');
        session_start();
    }

    public function loadPrototype(WebPrototype $prototype) {
        $prototype->initialize($this);
        $prototype->registerRoutes($this->routeDatabase);
        define('__DEBUG__', $this->config->debug);
        $this->templateEngine = new TemplateEngine();
        $this->assetsRegister->initialize($this);
        $this->languagerepo->addSource(new FileDataSource(__ROOT__ . '/framework/assets/strings/'));
        $this->routeDatabase->initializeControllers();
    }

    /**
     * @return Config
     */
    public function getConfig() {
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
     * @return LanguageRepository
     */
    public function getLanguageRepository() {
        return $this->languagerepo;
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
            return new PlainTextResponse($this->languagerepo->getEntry('smooth_error_404'));
        return $resolvedRoute->buildResponse($this, $request);
    }

}