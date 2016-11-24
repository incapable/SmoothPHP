<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * OctetStream.php
 * Description
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class OctetStream extends Response {

    public function build(Kernel $kernel, Request $request) {
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        if (is_array($this->controllerResponse) && isset($this->controllerResponse['type']))
            header('Content-Type: ' . $this->controllerResponse['type']);
        else
            header('Content-Type: application/octet-stream');

        if (is_array($this->controllerResponse) && isset($this->controllerResponse['filename'])) {
            $filename = $this->controllerResponse['filename'];
        } else {
            $url = (is_array($this->controllerResponse) && isset($this->controllerResponse['url'])) ? $this->controllerResponse['url'] : $this->controllerResponse;
            $pathSegments = explode('/', $url);
            $filename = end($pathSegments);
        }

        header('Content-Disposition: ' . (strpos($this->controllerResponse['type'], 'text/') == 0 ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
        header('Access-Control-Allow-Origin: *');
    }

    protected function sendBody() {
        if (is_array($this->controllerResponse) && isset($this->controllerResponse['url']))
            $response = $this->controllerResponse['url'];
        else
            $response = $this->controllerResponse;

        readfile($response);
    }

}