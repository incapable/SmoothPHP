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

class PlainTextResponse extends Response {
    
    protected function sendHeaders() {
        parent::sendHeaders();
        header('Content-Type: text/plain; charset=utf-8');
    }
    
    protected function sendBody() {
        echo $this->controllerResponse;
    }
    
}
