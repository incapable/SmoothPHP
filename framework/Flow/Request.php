<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Request.php
 * Class that represents a "request", be it a browser request or a code-generated request.
 */

namespace SmoothPHP\Framework\Flow;

class Request {
    private $get, $post, $server;
    
    public function __construct(array $get, array $post, array $server) {
        $this->get = new VariableSource($get);
        $this->post = new VariableSource($post);
        $this->server = new VariableSource($server);
    }
    
    /**
     * @param string $scope
     * @return \SmoothPHP\Framework\Flow\VariableSource
     */
    public function __get($scope) {
        switch($scope) {
            case "get":
            case "post":
            case "server":
                return $this->{$scope};
            default:
                throw new \Exception("Invalid scope.");
        }
    }
    
}
