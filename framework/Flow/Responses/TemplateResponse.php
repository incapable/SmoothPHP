<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateResponse.php
 * A template-based response, passed to the template engine.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class TemplateResponse extends Response {
    private $built;

    public function __construct($controllerResponse) {
        parent::__construct($controllerResponse);
    }

    public function build(Kernel $kernel, Request $request) {
        ob_start();
        var_dump($kernel->getTemplateEngine()->fetch($this->controllerResponse));
        $this->built = ob_get_clean();
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        header('Content-Type: text/html; charset=utf-8');
    }

    protected function sendBody() {
        echo $this->built;
    }

}
