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

use SmoothPHP\Framework\Authentication\AuthenticationManager;
use SmoothPHP\Framework\Cache\Assets\AssetsRegister;
use SmoothPHP\Framework\Core\Abstracts\WebPrototype;
use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Flow\Responses\AlternateErrorResponse;
use SmoothPHP\Framework\Flow\Responses\PlainTextResponse;
use SmoothPHP\Framework\Flow\Routing\RouteDatabase;
use SmoothPHP\Framework\Localization\FileDataSource;
use SmoothPHP\Framework\Localization\LanguageRepository;
use SmoothPHP\Framework\Templates\TemplateEngine;

class Kernel {
    // Site data
    private $config;
    private $routeDatabase;

    // Runtime
    private $errorHandler;
    private $templateEngine;
    private $assetsRegister;
    private $mysql;
    private $authentication;
    private $languagerepo;

    public function __construct() {
        $this->config = new Config();
        $this->authentication = new AuthenticationManager();
        $this->errorHandler = array($this, 'handleError');
    }

    public function loadPrototype(WebPrototype $prototype) {
        session_name('sm_sid');
        session_start();

        $this->routeDatabase = new RouteDatabase();
        $this->assetsRegister = new AssetsRegister();
        $this->languagerepo = new LanguageRepository($this);
        $this->languagerepo->addSource(new FileDataSource(__ROOT__ . 'framework/meta/assets/strings/'));
        $this->templateEngine = new TemplateEngine();
        $this->assetsRegister->initialize($this);
        $prototype->initialize($this);
        if ($this->config->authentication_enabled)
            $this->authentication->initialize($this);
        else
            $this->authentication = null;
        $this->languagerepo->addSource(new FileDataSource(__ROOT__ . 'src/assets/strings/'));
        $prototype->registerRoutes($this->routeDatabase);
        $this->routeDatabase->initializeControllers($this);
    }

    public function error($message) {
        global $request;
        if (isset($request->meta->route)) {
            $type = new $request->meta->route['content-type'](null);
            if ($type instanceof AlternateErrorResponse) {
                $type->buildErrorResponse($message);
                return $type;
            }
        }

        return call_user_func($this->errorHandler, $message);
    }

    public function setErrorHandler($errorHandler) {
        if (!is_callable($errorHandler))
            throw new \InvalidArgumentException('$errorHandler is not callable');

        $this->errorHandler = $errorHandler;
    }

    private function handleError($message) {
        return new PlainTextResponse($message);
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
     * @return AuthenticationManager
     */
    public function getAuthenticationManager() {
        if (!$this->config->authentication_enabled)
            throw new \RuntimeException("Authentication is not enabled");
        return $this->authentication;
    }

    /**
     * @return LanguageRepository
     */
    public function getLanguageRepository() {
        return $this->languagerepo;
    }

    /**
     * @param Request $request The request that determines how the response is made, and how it is given.
     * @return \SmoothPHP\Framework\Flow\Responses\Response|boolean
     */
    public function getResponse(Request $request) {
        $resolvedRoute = $this->routeDatabase->resolve($request);
        if (!$resolvedRoute) {
            http_response_code(404);
            return $this->error($this->languagerepo->getEntry('smooth_error_404'));
        }
        return $resolvedRoute->buildResponse($this, $request);
    }

}