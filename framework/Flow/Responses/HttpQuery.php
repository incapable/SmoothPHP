<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * HttpQuery.php
 * A response based on http_build_query
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class HttpQuery extends Response implements AlternateErrorResponse {
    private $built;

    public function buildErrorResponse($message) {
        $this->controllerResponse = array(
            'error' => $message
        );
    }

    public function build(Kernel $kernel, Request $request) {
        $this->built = http_build_query($this->controllerResponse);
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        header('Content-Type: application/x-www-form-urlencoded; charset=utf-8');
    }

    protected function sendBody() {
        echo $this->built;
    }

}