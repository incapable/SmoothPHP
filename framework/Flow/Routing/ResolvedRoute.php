<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ResolvedRoute.php
 * Class representing a route found after parsing the request.
 */

namespace SmoothPHP\Framework\Flow\Routing;

class ResolvedRoute {
    private $route;
    private $parameters;
    
    public function __construct(array &$route, array $parameters) {
        $this->route = $route;
        $this->parameters = $parameters;
    }
}