<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * PlainTextResponse.php
 * A text-based response.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class RedirectResponse extends Response {
    private $args;
    private $header;

    public function __construct($controllerResponse, array $args = array()) {
        parent::__construct($controllerResponse);
        $this->args = $args;
    }

    public function build(Kernel $kernel, Request $request) {
        $routes = $kernel->getRouteDatabase();

        if ($routes->getRoute($this->controllerResponse)) {
            $this->header = call_user_func_array(array($routes, 'buildPath'), array_merge(array($this->controllerResponse), $this->args));
        } else
            $this->header = $this->controllerResponse;
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        header('Location: ' . $this->header);
    }

    protected function sendBody() {
    }

}