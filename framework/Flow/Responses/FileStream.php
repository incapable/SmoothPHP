<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileStream.php
 * Response that will yield a file to the browser.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class FileStream extends Response {
    private $options;
    private $request;

    public function build(Kernel $kernel, Request $request) {
        $options = is_array($this->controllerResponse) ? $this->controllerResponse : array('url' => $this->controllerResponse);
        $this->request = $request;
        $this->options = array_merge(array(
            'type' => 'application/octet-stream',
            'filename' => end((explode('/', $options['url']))),
            'cache' => false,
            'cors' => true
        ), $options);
    }

    protected function sendHeaders() {
        parent::sendHeaders();

        header('Content-Type: ' . $this->options['type']);
        header('Content-Disposition: ' . (strpos($this->controllerResponse['type'], 'text/') == 0 ? 'inline' : 'attachment') . '; filename="' . $this->options['filename'] . '"');
        if ($this->options['cors'])
            header('Access-Control-Allow-Origin: *');

        if ($this->options['cache']) {
            $eTag = md5_file($this->options['url']);
            $lastModified = filemtime($this->options['url']);

            header('Cache-Control: max-age=86400, private');
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
            header('Pragma: private');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $lastModified));
            header('ETag: ' . $eTag);

            if ($this->request->server->HTTP_IF_MODIFIED_SINCE && $lastModified > strtotime($this->request->server->HTTP_IF_MODIFIED_SINCE)) {
                header('HTTP/1.1 304 Not modified');
                exit();
            }
            if ($this->request->server->HTTP_IF_NONE_MATCH && $this->request->server->HTTP_IF_NONE_MATCH == $eTag) {
                header('HTTP/1.1 304 Not modified');
                exit();
            }
        }
    }

    protected function sendBody() {
        readfile($this->options['url']);
    }

}