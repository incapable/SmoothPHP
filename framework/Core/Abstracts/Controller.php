<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Controller.php
 * Abstract controller style, doesn't contain actual implementation.
 */

namespace SmoothPHP\Framework\Core\Abstracts;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Responses\RedirectResponse;
use SmoothPHP\Framework\Flow\Responses\TemplateResponse;

abstract class Controller {

    public function onInitialize(Kernel $kernel) {}

    protected static function render($template, array $templateArgs = array()) {
        return new TemplateResponse($template, $templateArgs);
    }

    protected static function redirect() {
        $route = func_get_arg(0);
        $args = func_get_args();
        $args = array_splice($args, 1);
        return new RedirectResponse($route, $args);
    }

}