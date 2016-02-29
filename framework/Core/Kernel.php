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

class Kernel {
    private $routeDatabase;
    
    public function __construct() {
        $this->routeDatabase = new RouteDatabase();
    }
    
    public function loadPrototype(WebPrototype $prototype) {
        $prototype->registerRoutes($this->routeDatabase);
    }
    
}