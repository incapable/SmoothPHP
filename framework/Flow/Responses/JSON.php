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

class JSON extends Response {
    private $built;

    public function build(Kernel $kernel, Request $request) {
        $this->built = json_encode($this->controllerResponse);
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        header('Content-Type: application/json; charset=utf-8');
    }

    protected function sendBody() {
        echo $this->built;
    }

}