<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Website.php
 * Default WebPrototype implementation, named "Website" by default as defined in /public/index.php
 */

use SmoothPHP\Framework\Core\WebPrototype;
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Routing\RouteDatabase;

class Website extends WebPrototype {
    
    public function initialize(Kernel $kernel) {
        $config = $kernel->getConfig();

        $config->mysql_enabled = true;
        $config->mysql_database = 'test';
        $config->mysql_user = 'root';
        $config->mysql_password = 'root';
    }
    
    public function registerRoutes(RouteDatabase $routes) {
        $routes->register(array(
            'name' => 'index',
            'path' => '/',
            'controller' => \Test\Controllers\TestController::class,
            'call' => 'index'
        ));
    }

}