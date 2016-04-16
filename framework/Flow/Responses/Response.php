<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Response.php
 * Class that represents a "response", to be sent to the caller.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

abstract class Response {
    protected $controllerResponse;

    public function __construct($controllerResponse) {
        $this->controllerResponse = $controllerResponse;
    }

    public abstract function build(Kernel $kernel, Request $request);

    protected function sendHeaders() {
        header('X-Powered-By: SmoothPHP');
    }

    protected abstract function sendBody();

    public function send() {
        $this->sendHeaders();
        $this->sendBody();
    }

}
