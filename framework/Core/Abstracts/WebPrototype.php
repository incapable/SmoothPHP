<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * WebPrototype.php
 * Prototype for a website.
 */

namespace SmoothPHP\Framework\Core\Abstracts;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Routing\RouteDatabase;

abstract class WebPrototype {

    public abstract function initialize(Kernel $kernel);

    public abstract function registerRoutes(RouteDatabase $routes);

}