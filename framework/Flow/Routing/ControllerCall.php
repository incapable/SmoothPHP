<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ControllerCall.php
 * A parsed controller call, that is responsible for reading and later passing the right arguments.
 */

namespace SmoothPHP\Framework\Flow\Routing;

use SmoothPHP\Framework\Cache\Assets\AssetsRegister;
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Flow\Requests\Request;

class ControllerCall {
    private $request, $kernel, $mysql, $assetsRegister;
    private $parameters;

    private $callable;
    private $controllerArgs;

    public function __construct($controller, $call) {
        $this->controllerArgs = array();
        $this->parameters = array();

        $this->callable = array(new $controller(), $call);
        $method = new \ReflectionMethod($controller, $call);
        $i = -1;

        foreach ($method->getParameters() as $parameter) {
            $className = $parameter->getClass() ? $parameter->getClass()->name : null;
            switch ($className) {
                case Request::class:
                    $this->controllerArgs[] = &$this->request;
                    break;
                case Kernel::class:
                    $this->controllerArgs[] = &$this->kernel;
                    break;
                case AssetsRegister::class:
                    $this->controllerArgs[] = &$this->assetsRegister;
                    break;
                case MySQL::class:
                    $this->mysql = -1; // Make sure the later isset fills this value
                    $this->controllerArgs[] = &$this->mysql;
                    break;
                default: // Mixed-type arg, url-argument
                    $this->parameters[++$i] = null;
                    $this->controllerArgs[] = &$this->parameters[$i];
                    break;
            }
        }
    }

    /**
     * @return \SmoothPHP\Framework\Flow\Responses\Response|mixed
     */
    public function performCall(Kernel $kernel, Request $request, array $args) {
        $this->kernel = $kernel;
        $this->request = $request;
        if (isset($this->mysql)) // MySQL should only be initialized on-demand
            $this->mysql = $kernel->getMySQL();
        $this->assetsRegister = $kernel->getAssetsRegister();

        $i = 0;
        foreach ($args as $arg) {
            $this->parameters[$i++] = $arg;
        }

        return call_user_func_array($this->callable, $this->controllerArgs);
    }

}