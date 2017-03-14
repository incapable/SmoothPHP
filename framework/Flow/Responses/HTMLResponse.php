<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * HTML.php
 * A html-based response.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class HTMLResponse extends Response {

    public function build(Kernel $kernel, Request $request) {
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        header('Content-Type: text/html; charset=utf-8');
    }

    protected function sendBody() {
        echo $this->controllerResponse;
    }

}
