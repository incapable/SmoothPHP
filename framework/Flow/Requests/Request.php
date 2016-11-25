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

namespace SmoothPHP\Framework\Flow\Requests;

class Request {
    private $getr, $postr, $serverr;
    public $meta;

    /**
     * @return \SmoothPHP\Framework\Flow\Requests\Request
     */
    public static function createFromGlobals() {
        return new Request($_GET, $_POST, $_SERVER);
    }

    public function __construct(array $get, array $post, array $server) {
        $this->getr = new VariableSource($get);
        $this->postr = new VariableSource($post);
        $this->serverr = new VariableSource($server);
        $this->meta = new \stdClass();
    }

    /**
     * @param $scope
     * @return VariableSource
     * @throws \Exception
     */
    public function __get($scope) {
        switch ($scope) {
            case "get":
            case "post":
            case "server":
                return $this->{$scope . 'r'};
            default:
                throw new \Exception("Invalid scope.");
        }
    }

}
